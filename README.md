# Stadium & Pitch Booking API

A simple Laravel API to manage stadiums, pitches, and booking slots.  
Includes a minimal UI (Blade + Bootstrap) for testing.  

---

## Features

- Seeders to create stadiums and pitches (configurable via `.env`)
- Two main APIs:
  1. **Available Slots API** – returns only future, unbooked slots (60 or 90 minutes)
  2. **Booking API** – book a slot with conflict prevention
- Validation with `FormRequest`:
  - Date cannot be in the past
  - Duration must be 60 or 90 minutes
  - No overlapping bookings
- Basic Bootstrap UI (not fancy, just functional)

---

##  Installation

1. Clone the repo
   ```bash
   git clone https://github.com/ahmedsafroot/booking-task
   cd booking-task
   ```

2. Install dependencies
   ```bash
   composer install
   npm install && npm run build
   ```

3. Configure environment
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. Database setup
   ```bash
   php artisan migrate --seed
   ```

   > Seeder will create stadiums & pitches.  
   > Numbers are configurable via `.env`:
   > ```env
   > SEED_STADIUM_COUNT=2
   > SEED_PITCH_COUNT=3
   > ```

---

## API Endpoints

### 1. Get Available Slots
```
GET /api/pitches/{pitch_id}/slots?date=2025-09-24&duration=90
```

**Response**
```json
{
    "status": true,
    "message": "Success",
    "data": {
        "slots": [
            {
                "start_time": "15:30",
                "end_time": "17:00"
            },
            {
                "start_time": "21:30",
                "end_time": "23:00"
            }
        ]
    }
}
```

---

### 2. Book a Slot
```
POST /api/bookings
```

**Payload**
```json
{
  "pitch_id": 1,
  "date": "2025-09-24",
  "start_time": "08:00",
  "end_time": "09:30"
}
```

**Success Response**
```json
{
    "status": true,
    "message": "Booking a slot successfully",
    "data": {
        "slot": {
            "pitch_id": 2,
            "date": "2025-09-27",
            "start_time": "15:00",
            "end_time": "16:30",
            "updated_at": "2025-09-27T10:32:35.000000Z",
            "created_at": "2025-09-27T10:32:35.000000Z",
            "id": 4
        }
    }
}
```

**Error Response**
```json
{
    "status": false,
    "message": "Validation failed",
    "errors": {
        "slot": [
            "Slot already booked"
        ]
    }
}
```

---

## UI (Blade + Bootstrap)

A simple UI is available at:

```
http://127.0.0.1:8000
```

- Select pitch, date, and duration  
- Click **Check Available Slots**  
- Book directly with one click  
- Success & errors shown in a Bootstrap modal  

---
##  Postman Collection
A ready-to-use Postman collection is included:  
[Booking Task.postman_collection.json](Booking Task.postman_collection.json)

Import it into Postman, set `{{base_url}}` in your Postman environment.

---
## Development

- Laravel 12
- PHP 8.2+
- Bootstrap 5.3 (via CDN) Should Internet Access Working
- Uses Eloquent Factories + Seeders


