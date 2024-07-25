# E-commerce Product Management API

## Project Description

This project is a Laravel-based application providing a product management API. It includes user authentication and authorization, product CRUD operations, category management, and product reviews. The application uses MySQL for the database and Redis for caching.

## Project Components

- **Laravel App**: The core application framework.
- **MySQL DB**: Used for persistent data storage.
- **Redis Cache**: Used for caching data to improve performance.

## Setup for Local Development

### Prerequisites

- [Docker](https://www.docker.com/products/docker-desktop) installed
- [Laravel Sail](https://laravel.com/docs/8.x/sail) (included with Laravel)

### Steps

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd <repository-directory>
    ```

2. **Install dependencies:**
    ```bash
    ./vendor/bin/sail composer install
    ```
3. **Copy the .env file and set environment variables:** 
    ```bash
    cp .env.example .env
    ./vendor/bin/sail artisan key:generate
    ```
4. **Start the development environment:**
    ```bash
    ./vendor/bin/sail up
    ```
5. **Run the database migrations and seeders:**
    ```bash
    ./vendor/bin/sail artisan migrate --seed
    ```
6. **Access the application:**
    The application should now be running at `http://localhost:8888`.

## API Documentation 

### Authentication 
 
- **Register**  
  - **URL:**  `POST /register`
 
  - **Headers:** 

```http
Accept: application/json
```
 
  - **Body:** 

```json
{
  "name": "string",
  "email": "string",
  "password": "string",
  "password_confirmation": "string"
}
```
 
  - **Response:**  `201 Created`
 
- **Login**  
  - **URL:**  `POST /login`
 
  - **Headers:** 

```http
Accept: application/json
```
 
  - **Body:** 

```json
{
  "email": "string",
  "password": "string"
}
```
 
  - **Response:**  `200 OK`

```json
{
  "token": "string"
}
```

### Products 
 
- **Get all products**  
  - **URL:**  `GET /products`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Query Parameters:**  
    - `category_name`: Filter by category name (optional)
 
    - `category_id`: Filter by category ID (optional)
 
    - `product_name`: Filter by product name (optional)
 
    - `order_by`: Order by field (default: `rating`)
 
    - `direction`: Order direction (`asc` or `desc`, default: `asc`)
 
    - `offset`: Pagination offset (default: `0`)
 
    - `limit`: Pagination limit (default: `10`)
 
  - **Response:**  `200 OK`
 
- **Get a product**  
  - **URL:**  `GET /products/{product}`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Response:**  `200 OK` or `404 Not Found`
 
- **Create a product (Admin)**  
  - **URL:**  `POST /products`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Body:** 

```json
{
  "name": "string",
  "price": "number",
  "category_id": "integer"
}
```
 
  - **Response:**  `201 Created` or `422 Unprocessable Entity`
 
- **Update a product (Admin)**  
  - **URL:**  `PUT /products/{product}`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Body:** 

```json
{
  "name": "string",
  "price": "number",
  "category_id": "integer"
}
```
 
  - **Response:**  `200 OK` or `404 Not Found`
 
- **Delete a product (Admin)**  
  - **URL:**  `DELETE /products/{product}`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Response:**  `200 OK` or `404 Not Found`

### Reviews 
 
- **Get product reviews**  
  - **URL:**  `GET /products/{product}/reviews`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Response:**  `200 OK` or `404 Not Found`
 
- **Add product review**  
  - **URL:**  `POST /products/{product}/reviews`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Body:** 

```json
{
  "rating": "number",
  "review": "string"
}
```
 
  - **Response:**  `200 OK` or `422 Unprocessable Entity`

### User Management (Admin) 
 
- **Set User Role**  
  - **URL:**  `POST /users/role`
 
  - **Headers:** 

```http
Accept: application/json
Authorization: Bearer {token}
```
 
  - **Body:** 

```json
{
  "user_id": "integer",
  "role": "string"
}
```
 
  - **Response:**  `200 OK` or `404 Not Found`

## Running Tests 

To run the tests, use the following command:


```bash
./vendor/bin/sail test
```

This command will run all unit and feature tests to ensure the application is functioning correctly.

