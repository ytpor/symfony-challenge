# Symfony Challenge

The challenge is to develop a function to estimate delivery time.

## Prerequisite

* You have an environment that can run Symfony.
* You have installed `composer` in your environment.

## Get started

* Clone this repository
* Change directory into the newly cloned repository
* Make a copy of `.env.example`, and name it `.env`.
* Install the packages `composer install`
* Run the test `php bin/phpunit`

## Notes

* The function can be found at `src/Service/EstimatorService.php`
* The test cases can be found at `test/EstimatorServiceTest.php`
    * Run the test `php bin/phpunit tests/EstimatorServiceTest.php`
* Only entity properties relevant to this challenge is defined.
* Find the entities in `src/Entity`
* A console command be found at `test/EstimateDeliveryCommand.php`.
    * Run the console command `php bin/console app:estimate-delivery`

## Assumptions

Here are the minimum number of days an order will be delivered based on shipping method and location.

| | Express | Standard |
| --- | --- | --- |
| Local | 3 | 5 |
| International | 5 | 8 |

*One extra day of buffer is added to the returned message, eg. when delivery takes 5 days, the message would read "Delivery in 5-6 business days"*

## Test Matrix

This table indicates the minimum number of days an order will be delivered based on shipping method and location, and also the number of days a product will be restocked.

| | Express | Express (days to restock) | Standard | Standard (days to restock) | Message |
| --- | --- | --- | --- | --- | --- |
| Local | 3 | | | | Delivery in 3-4 business days |
| Local | 3 | 10 | | | Delivery in 10-11 business days |
| Local | 3 | 2 | | | Delivery in 3-4 business days |
| Internatinal | 5 | | | | Delivery in 5-6 business days |
| Internatinal | 5 | 10 | | | Delivery in 10-11 business days |
| Internatinal | 5 | 2 | | | Delivery in 5-6 business days |
| Local | | | 5 | | Delivery in 5-6 business days |
| Local | | | 5 | 10 | Delivery in 10-11 business days |
| Local | | | 5 | 2 | Delivery in 5-6 business days |
| Internatinal | | | 8 | | Delivery in 8-9 business days |
| Internatinal | | | 8 | 10 | Delivery in 10-11 business days |
| Internatinal | | | 8 | 2 | Delivery in 8-9 business days |
