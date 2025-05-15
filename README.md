# Weather Forecast & Subscription API

A Laravel-based application that provides weather updates and subscription services. Users can subscribe to weather updates, confirm their subscriptions, and unsubscribe using a token-based system.

---

## Features

* Search for cities using the Weather API.
* Subscribe to weather updates for a specific city and frequency (hourly/daily).
* Confirm email subscriptions through a unique token.
* Unsubscribe from updates using email and token verification.

---

## Prerequisites

* Docker (v20.10 or later)
* Docker Compose (v2.2 or later)

---

## Installation and Setup

1. **Clone the repository:**

```
git clone https://github.com/Kuzmenko-Oleksandr/weather-api.git
cd weather-api
```

2. **Copy Environment Configuration:**

```
cp .env.example .env
```

3. **Update .env File:**

Set the following variables:

```
APP_URL=http://localhost:8000
WEATHER_API_KEY=YOUR_WEATHER_API_KEY_HERE

MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
```

4. **Build and Start Docker Containers:**

```
docker-compose up -d --build
```

5. **Run Migrations:**

```
docker-compose exec app php artisan migrate
```

6. **Generate Application Key:**

```
docker-compose exec app php artisan key:generate
```

---

## Application Endpoints

* Main Application: `http://localhost:8000`
* Mailpit (Email Testing): `http://localhost:8025`

---

## Email Subscription Workflow

1. Visit the main application: `http://localhost:8000`.
2. Enter email and city. Select the frequency (hourly/daily) and subscribe.
3. Check the email in Mailpit and confirm the subscription using the link.
4. Unsubscribe by entering the email to receive an unsubscribe link.

---

## API Endpoints

### GET `/get-cities?query={query}`

Fetch city suggestions based on the query.

**Example Request:**

```
GET /get-cities?query=Kyiv
```

---

### POST `/api/subscribe`

Subscribe to weather updates.

**Request Body:**

```json
{
  "email": "test@example.com",
  "city": "Kyiv",
  "frequency": "daily"
}
```

**Response:**

```json
{
  "status": "success",
  "message": "Subscription created. Check your email to confirm."
}
```

---

### GET `/api/confirm/{token}`

Confirm the subscription using the token.

**Example Request:**

```
GET /api/confirm/{token}
```

---

### POST `/api/get-token`

Get the token associated with the email.

**Request Body:**

```json
{
  "email": "test@example.com"
}
```

**Response:**

```json
{
  "token": "randomGeneratedToken123"
}
```

---

### GET `/api/unsubscribe/{token}`

Unsubscribe using the token.

**Example Request:**

```
GET /api/unsubscribe/{token}
```

---

## Development Commands

### Rebuild Containers:

```
docker-compose up -d --build
```

---

### View Application Logs:

```
docker-compose logs -f app
```

---

### Run Artisan Commands:

```
docker-compose exec app php artisan migrate
```

---

### Clear Cache:

```
docker-compose exec app php artisan optimize:clear
```

---

## Notes

* Ensure `WEATHER_API_KEY` is correctly set in the `.env` file.
* If the email is not received, check Mailpit at `http://localhost:8025`.
* For testing email confirmation and unsubscribe functionality, check the logs in Mailpit.
