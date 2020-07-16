# RabbitMQ

### Producer/Consumer API
This project is developed to demonstrate how you can configure RabbitMQ to transfer messages between two independent apps. 
Also, Docker is used. Each application live in their own container and communication between broker, producer, and consumer is isolated from OS 
so you can easily deploy it to Kubernetes etc. 

### Installation
To bootstrap project run the following docker-compose command.  

```docker-compose  -f docker-compose.rabbitmq-broker.yml -f docker-compose.rabbitmq-producer.yml -f docker-compose.rabbitmq-consumer.yml up```

These applications require socket ext to be installed to communicate with each other. So you have to run this command to install it:

```docker-php-ext-install bcmath```
```docker-php-ext-install sockets```


After all containers are up you have to run composer install command in rabbitmq-producer container to finish Laravel project setup:

```docker exec -it rabbitmq-producer bash```
```composer install```
```php artisan key:generate```

Do the same for consumer app:

```docker exec -it rabbitmq-consumer bash```
```composer install```
```php artisan key:generate```

Copy the .env.example file ( cp .env.example .env ) in rabbitmq-producer and rabbitmq-consumer folders.


### Generate messages

You can use a web-based UI of RabbitMQ to check all exchanges and queues that are setup. This UI is accessible at ```http://localhost:15672```
Login with username guest and password guest.

To visualize details about RabbitMQ exchanges navigate to Exchange tab and check all available exchanges. 
Let's create a new exchange and call it ```com.rabbitmq.demo.queue```. Change Type to ```fanaut``` and save it. 
Before you continue you have to be familiar with different RabbitMQ exchange type so go to [RabbitMQ exchange types](https://www.rabbitmq.com/tutorials/amqp-concepts.html) 
and learn more about it.

To produce a message you can use `http://localhost/publish` in your browser and start using the Demo Producer endpoint.
This endpoint will produce a new message in RabbitMQ exchange com.rabbitmq.demo.queue.

### Consume messages

To consume messages you have to bind queues to already created exchange on the previous step. To do that go to the Queues tab and Add a new queue.
After successfully save go back to the Exchange tab and select ```com.rabbitmq.demo.queue``` exchange. Scroll down and find the Bindings tab.
Here we have to map our exchange and queue, so write already created queue name to Add binding from this exchange and save it.

That is it! You can add more queues to this exchange but for this demo, let's try to consume just one queue.

To consume messages from queue go back to project and run following command in rabbitmq-consumer container to start consuming messages:

```php artisan rabbitmq:consume --queue=queue-rabbitmq```

Here ```queue-rabbitmq``` is the name of the queue which you entered above. You can change it if needed.

### Supervisor 

This project is configured with a Supervisor. You can learn more about it at [Laravel site](https://laravel.com/docs/7.x/queues#supervisor-configuration).
The Supervisor will be responsible for monitor your queue:work process and automatically restart if it fails.  
