# Introduction

It's time for a deep dive into system design. In this session, we'll explore how asynchronous messaging, queues, and background tasks operate within distributed systems. In today’s scalable computing environments, it’s often impractical for servers to complete tasks instantly or in a single operation. That’s where asynchronous processing comes in: by defining tasks as messages, we can store, process, and remove them only after successful execution.

To bring these concepts to life, we'll walk through a practical example — automating Instagram posts. We'll examine how the Instagram API works, create and dispatch messages using the Symfony framework, schedule message processing with Linux cron jobs, and trigger email notifications using Amazon Simple Email Service (SES). We'll also briefly explore Celery, Kafka, RabbitMQ, and Redis as alternative tools. By the end of the talk, you'll gain both theoretical insight and hands-on skills you can apply to real-world business scenarios. This session is open to all skill levels, though basic familiarity with REST APIs, Symfony, and Linux will be helpful.

# Backend in Symfony

In this demo the Symfony backend uses `logger`, `mailer`, `messenger`, `twig`, `doctrine`, `orm` packages.