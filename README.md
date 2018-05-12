# brawijaya-hotel-api

Back-end server for Final Project of Web Programming course

## Pre-requirements
- PHP 7.1
- MySQL Database

## How to setup
1. Clone this repository
2. copy `.env.example` file to `.env` and make changes according to your DB server
3. Run `composer install` to install dependencies within the repository directory
4. Run `php artisan migrate`
5. Run `php artisan db:seed`
6. You can place this repository inside `htdocs` folder of your preferred web server **OR** you can use `php -S localhost:8000 -t public` command to open port `8000` and fire up the server.
7. Enjoy

## Error Messages

Error messages will be returned if you are accessing the resource with some errors. It might return `errors` field within the response and set `data` to be null so no data will be returned if error exist.

### Error template

Single Error:

```json
{
	"errors": [
		{
			"message": "Room not found"
		}
	]
}
```

Multiple Errors:

```json
{
	"errors": [
		{
			"field": "customer_name",
			"message": "The customer name field is required."
		},
		{
			"field": "customer_nin",
			"message": "The customer nin field is required."
		},
		{
			"field": "phone",
			"message": "The phone field is required."
		},
		{
			"field": "check_in",
			"message": "The check in field is required."
		},
		{
			"field": "check_out",
			"message": "The check out field is required."
		},
		{
			"field": "adult_capacity",
			"message": "The adult capacity field is required."
		},
		{
			"field": "children_capacity",
			"message": "The children capacity field is required."
		},
		{
			"field": "rooms",
			"message": "The rooms field is required."
		}
	]
}
```

Errors with additional data through `value` field:

```json
{
	"errors": [
		{
			"message": "Some rooms are occupied",
			"value": [
				3,
				9,
				2
			]
		}
	]
}
```

## Endpoints

### Fetch Public Holidays (2018 only)

`GET /public_holidays`

#### Parameters

None

#### Sample Request

None

#### Sample Response

```json
{
	"data": [
		{
			"date": "2018-01-01",
			"title": "Tahun Baru Masehi"
		},
		{
			"date": "2018-02-16",
			"title": "Tahun Baru Imlek"
		},
		{
			"date": "2018-03-17",
			"title": "Hari Raya Nyepi"
		},
		{
			"date": "2018-03-30",
			"title": "Jumat Agung"
		}
	]
}
```

---

### Fetch All Rooms

`GET /rooms`

#### Parameters

None

#### Sample Request

None

#### Sample Response

```json
{
	"data": [
		{
			"name": "Audy",
			"type": "Superior",
			"price": 400000
		},
		{
			"name": "Anggur",
			"type": "Superior",
			"price": 400000
		},
		{
			"name": "Jeruk",
			"type": "Deluxe",
			"price": 600000
		},
		{
			"name": "Nanas",
			"type": "Deluxe",
			"price": 600000
		}
	]
}
```

### Fetch Single Room

`GET /rooms/{room_id}`

#### Parameters

Parameter | Type | Description
--------- | ---- | -------------
`room_id` | int | the unique ID of room

#### Sample Request (URL)

`GET /rooms/8`

#### Sample Response

```json
{
	"data": {
		"name": "Anggur",
		"type": "Superior",
		"price": 400000,
		"holiday_price": 500000
	}
}
```

---

### Fetch All Reservations

`GET /reservations`

#### Parameters

NONE

#### Sample Request

NONE

#### Sample Response

```json
{
	"data": [
		{
			"id": 1,
			"customer_name": "Tukiman A",
			"customer_nin": "165150200111063",
			"phone": "081213141516",
			"check_in": "2018-05-21",
			"check_out": "2018-05-24",
			"adult_capacity": 6,
			"children_capacity": 9,
			"rooms": [
				{
					"name": "Melati",
					"type": "Superior",
					"price": 400000,
					"extra_bed": 0
				},
				{
					"name": "Nanas",
					"type": "Deluxe",
					"price": 600000,
					"extra_bed": 1
				}
			]
		},
		{
			"id": 2,
			"customer_name": "Tukiman B",
			"customer_nin": "165150200111064",
			"phone": "081213141517",
			"check_in": "2018-05-14",
			"check_out": "2018-05-18",
			"adult_capacity": 6,
			"children_capacity": 1,
			"rooms": [
				{
					"name": "Mandi Dalam",
					"type": "Superior",
					"price": 400000,
					"extra_bed": 0
				},
				{
					"name": "Melati",
					"type": "Superior",
					"price": 400000,
					"extra_bed": 1
				}
			]
		}
	]
}
```


### Fetch Single Reservations

`GET /reservations/{reservation_id}`

#### Parameters

Parameter | Type | Description
--------- | ---- | -------------
`reservation_id` | int | the unique ID of reservation

#### Sample Request (URL)

`GET /reservations/2`

#### Sample Response

```json
{
	"data": {
		"customer_name": "Tukiman B",
		"customer_nin": "165150200111064",
		"phone": "081213141517",
		"check_in": "2018-05-14",
		"check_out": "2018-05-18",
		"adult_capacity": 6,
		"children_capacity": 1,
		"rooms": [
			{
				"name": "Mandi Dalam",
				"type": "Superior",
				"price": 400000,
				"extra_bed": 0
			},
			{
				"name": "Melati",
				"type": "Superior",
				"price": 400000,
				"extra_bed": 1
			}
		]
	}
}
```


### Create Reservation

`POST /reservations`

#### Parameters

Parameter | Type | Description
--------- | ---- | -------------
`customer_name` | string | - 
`customer_nin` | int | NIN = National ID Number (NIK dlm Bahasa Indonesia :D ) (Max: 20 Digit)
`phone` | int | Max: 12 Digit
`check_in` | string | Format: `yyyy-mm-dd`. e.g.: `2018-06-12` 
`check_out` | string | Format: `yyyy-mm-dd`. e.g.: `2018-06-12` 
`adult_capacity` | int | - 
`children_capacity` | int | - 
`rooms` | array | see below 
`rooms.*.id` | int | ID of room to be reserved
`rooms.*.extra_bed` | boolean | with extra bed 

**Note: All the fields above are REQUIRED**

#### Sample Request (Body)

```json
{
	"customer_name": "Tukiman C",
	"customer_nin": "165150200111065",
	"phone": "081213141518",
	"check_in": "2018-06-09",
	"check_out": "2018-06-12",
	"adult_capacity": "6",
	"children_capacity": "3",
	"rooms": [
		{
			"id": 3,
			"extra_bed": true
		},
		{
			"id": 9,
			"extra_bed": true
		},
		{
			"id": 2,
			"extra_bed": false
		}
	]
}
```

#### Sample Response

```json
{
	"data": {
		"id": 6,
		"customer_name": "Tukiman C",
		"customer_nin": "165150200111065",
		"phone": "081213141518",
		"check_in": "2018-06-09",
		"check_out": "2018-06-12",
		"adult_capacity": "6",
		"children_capacity": "3",
		"rooms": [
			{
				"id": 2,
				"name": "Mandi Luar",
				"type": "Superior",
				"price": 400000,
				"extra_bed": 0
			},
			{
				"id": 3,
				"name": "Dahlia",
				"type": "Superior",
				"price": 400000,
				"extra_bed": 1
			},
			{
				"id": 9,
				"name": "Jeruk",
				"type": "Deluxe",
				"price": 600000,
				"extra_bed": 1
			}
		],
		"total_price": 5200000
	}
}
```

