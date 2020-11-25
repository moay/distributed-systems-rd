# Distributed systems - a coding challenge

## Setup

This repo contains several services which are meant to run together and can be launched using docker compose.

The setup should be easy to run.

```
docker-compose up
```

### Contained services

* Order microservice (a microservice that handles orders and publishes order related events), consisting of
    * php-fpm
    * nginx
    * mariaDB
* Voucher microservice (a microservice that is meant to handle voucher codes), consisting of
    * php-fpm
    * nginx
    * mariaDB
* RabbitMQ (used as a message bus for event based communication between the microservices)

### Dashboards

In order to allow playing around with the microservices, very basic dashboards are provided.

* Order microservice: `localhost:5001`
* Voucher microservice: `localhost:5002`

The dashboards give you an impression of whats in the system and allow to create new orders and mark them as delivered.
Due to time pressure, this part did noch receive much love. For a real world ui, I would prefer to create properly
authenticated SPAs based on Vue.js for this task and instantiate a proper webpack based asset pipeline.

For further investigation of queue and database status, use the subsystem uis or local clients.

#### RabbitMQ

For a quick setup, I used the rabbitmq image provided by bitnami.

The management ui is available at `localhost:15672`. The username is `user`, the password is `bitnami`.

#### Databases

Each microservice will be equipped with its own database server and database.

Both database servers will be exposed and accessible with username `root`.

* Orders database: `localhost:33061`, password `order-database-password`
* Vouchers database: `localhost:33062`, password `voucher-database-password`

## Useful commands

When running via docker, supervisord will make sure that the necessary consumers are running and consuming
the messages that are floating around. These commands can of course be executed manually (stop supervisord before doing
so), here is a quick reference:

### Order microservice

* Creating a new order `docker-compose exec order-php-fpm bin/console order:create [value]` - The optional `value`
  represents the *net* value of the order and defaults to `150`
* Marking the earliest not delivered order as delivered `docker-compose exec order-php-fpm bin/console order:mark-as-delivered`
* Sending order messages to RabbitMQ `docker-compose exec order-php-fpm bin/console messenger:consume async`

### Voucher microservice

* Receiving order messages from RabbitMQ `docker-compose exec voucher-php-fpm bin/console messenger:consume rabbitmq_order_events`
* Processing received order information `docker-compose exec voucher-php-fpm bin/console messenger:consume async`

## Important thoughts

The focus of this implementation is to demonstrate how I would setup two microservices and design communication between
them. As the time available for the project was very limited, I had to focus on some areas of the implementation and
leave a lot of things out.

### Things I accounted for:

* Maximum possible automation of the process. This includes
    * Automated retries of failed events
    * Asynchronous communication of events towards the message bus in order to easily enable automated retries
* Expecting communication fails. When an event fails, it will be retried and stored upon final failure, so that it can
  easily be inspected and retried manually (or by downstream fail handling routines)
* Fully working docker setup. This includes automatically launched queue workers and database setup.
* Testable code. Both microservices fully rely on dependency injection and are ready to be tested.
* Basic testing. Covering both microservices with fully blown tests was not possible due to the limited time. I added
  some tests to verify that the process in itself is consistent and that the business strategy is properly applied. 

### Things that were left out:

* Realistic data structures. The order events and the voucher events do both not contain any sophisticated data structures.
    * The orders do not contain products, customer related information, and so on.
    * The vouchers do not contain information about the related customer (which might be a hard limit when redeeming a
    voucher in real life and in this case should be stored with the voucher).
* Backporting of vouchers into the order database. This maybe a useful thing to do in a real life scenario, f.i. in order
  to be able to tell whether or not a voucher has been created for an order. I have thought of implementing this, but
  decided to not do this for mainly two reasons.
    * Backporting the information would not add any value to the prototype
    * Having this information in the order database actually might not be wanted, as it is not part of the order
      microservices domain/context.
* Terminal failure handling. If asynchronous tasks fail repeatedly, the final measure taken is storing them in the
  database. The next step in a real world scenario would depend on the general setup and on how far automation is taken.
  One solution could be a manual review of failed messages/processes, but this task could also be part of automated or
  semi-automated procedures.
* App monitoring, process monitoring and error reporting. For the purpose of this prototype, I expect both microservices
  to be running. In order to take this setup to a production environment, setting up monitoring tools would be crucial
  to make sure that all processes and systems are up and running. Also, exceptions should be reported in some sort of
  error monitoring tool like sentry.
* Things that are currently placed in `src/Domain/MessageBus` (in both microservices) are addressing shared domain logic
  regarding communication between the microservices. As the asynchronous communication bases on those classes and the
  classes may be useful for further usage in other microservices, they should be held in a shared composer package.
* Event persistency. Currently events are fired once and then processed in asynchronous processes. If a message from the
  order microservice fails, it may appear back in the dead letter exchange. Based on a business strategy (not part of
  this prototype), the event which initially triggered the message should maybe be fired again. In this case, it would
  be useful to have an event id which could be resubmitted with the new instance of the event. This would allow other
  downstream processes to determine if the event has already been handled and can therefore be ignored. Not providing an
  event id or message id forces the microservices to check the message contents and decide based on those whether to do
  anything or not (which could also be a valid strategy).
* Proper RabbitMQ setup for multiple consumers. Currently, the order-microservice produces messages to one fanout queue
  on RabbitMQ. The voucher microservice will consume those and take them and acknowledge towards RabbitMQ. This will
  take the messages out of the queue. If at some point multiple microservices enter the game and might be interested in
  order related events, a queue/exchange management strategy would need to be discussed and implemented in order to have
  proper queues for each microservice.
* Security between microservices. For this prototype, the communication between the microservices and RabittMQ is not
  encrypted. I'd recommend to at least force SSL when leaving a dev environment. Also I'd recommend signing message
  contents and validating that the message has been issued by a trustworthy system.
* Performance considerations on the database. As the database might grow quickly, using indexes seems like a good idea.
* Full testing. Both microservices would need to be tested in unit and integration tests, also end-to-end tests would be
  useful to test the entire setup.  
      
## Fail handling strategies

### Order microservice

The order microservice sends order related events towards the message bus. This could fail (f.i. if the message
bus is not available due to network issues or rabbitmq not beeing running).

Sending the events towards the message bus will be executed asynchronously. In case of failure, the microservice will
retry to deliver the message up to 5 times with an initial delay of 5 minutes. After each failed retry, the delay until
the next retry will be raised. The last retry will have a delay of round about 52 hours.

If after 5 retries the message bus did not accept the message, the failed message will be stored in a queue for manual
inspection and retry.

### RabbitMQ

When a message from the order microservice is received, its time-to-live will be set to 2 days. If the message is not
consumed within this time, it will be delivered to the dead letter exchange.

The voucher service will consume the messages. We expect valid messages from the order services, although the message
validation is quite basic and should be improved when more microservices are added to the system. If the message content
seems not to be valid, the message will be rejected and RabbitMQ will deliver it to the dead letter exchange.

If the message was accepted, it's contents will be passed to an internal asynchronous process of the voucher microservice.
The storing of the message might fail, if so, RabbitMQ will try to redeliver the message several times. In case of
terminal failure, the message will be stored as failed job in the microservice (or in the dead letter exchang if this
also fails).

In the current setup, there is no consumer for the dead letter exchange. A terminal failure handling strategy would have
to decide what to do with those messages and how to react. A valid approach would be to consume the dlx in the order
microservice and retrigger the event.

### Voucher microservice

All messages from the message bus will be validated and stored for further processing via a local asynchronous queue
worker. Processing of the events and possible voucher creation will be tried up to three times. Terminal failure will
result in a failed local job which should be monitored and acted upon in a real-world scenario.