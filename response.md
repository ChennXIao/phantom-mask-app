## Requirement Completion Rate
* [x] List pharmacies, optionally filtered by specific time and/or day of the week.  
  * Implemented at `GET /api/pharmacies` API.
* [x] List all masks sold by a given pharmacy with an option to sort by name or price.  
  * Implemented at `GET /api/pharmacies/{pharmacy}/masks` API.
* [x] List all pharmacies that offer a number of mask products within a given price range, where the count is above, below, or between given thresholds.  
  * Implemented at `GET /api/pharmacies/filter-by-mask-count` API.
* [x] Show the top N users who spent the most on masks during a specific date range.  
  * Implemented at `GET /api/orders/top-spenders` API.
* [x] Process a purchase where a user buys masks from multiple pharmacies at once.  
  * Implemented at `POST /api/orders` API.
* [x] Update the stock quantity of an existing mask product by increasing or decreasing it. (update for a pharmacy, because there is a same mask sold by different pharmacy)
  * Implemented at `PATCH /api/pharmacies/{pharmacy}/masks/{mask}` API.
* [x] Create or update multiple mask products for a pharmacy at once, including name, price, and stock quantity.
  * Implemented at `POST /api/pharmacies/{pharmacy}/masks/batch` API.
* [x] Search for pharmacies or masks by name and rank the results by relevance to the search term.  
  * Implemented at `GET /api/search` API.

---

## API Document

API documentation is available via Swagger UI. After starting the application, you can access the [document](http://localhost:8000/api/documentation).

---

## Import Data Commands

Please run these two script commands to migrate the data into the database.

```bash
php artisan import:pharmacy-data
php artisan import:customers-data
```

---
## Test Coverage Report
I have written tests for the implemented API endpoints. You can find them in the `tests/Feature` directory.

You can open it up test report from [here](http://localhost:5500/coverage/index.html) after running in generate coverage report command in docker container:

```bash
# Generate coverage report
$ XDEBUG_MODE=coverage php artisan test --coverage-html=coverage    

```
- hint: after running this command, `/coverage` will be output to your IDE, the report can be seen when you access `/coverage/index.html`. You can install extension like [Live Server](https://marketplace.visualstudio.com/items?itemName=ritwickdey.LiveServer) to open up `/coverage/index.html`.

You can run the test script by using the command below:
```bash
$ php artisan test

```
---
## Deployment
* To deploy the project locally using Docker, run the following commands:
```bash
# before docker 
cp .env.example .env
```
```bash
# Start the service using docker-compose
docker-compose up -d

# Access container
docker exec -i -t laravel-app bash
# docker container
$ composer install
$ php artisan key:generate
$ php artisan migrate
$ php artisan import:pharmacy-data
$ php artisan import:customers-data
```
---
## Additional Data
> The database schema is defined in `schema.dbml`. You can use a tool like dbdiagram.io to visualize the [ERD](https://dbdiagram.io/d/686fb17cf413ba35083f5564).
