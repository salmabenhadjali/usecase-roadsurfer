# **Fruits and Vegetables API Documentation**

## **Project Overview**

This project provides a RESTful API to manage collections of Fruits and Vegetables. It supports adding, listing, and filtering items while ensuring weight units are standardized to grams (`g`) or kilograms (`kg`). The application runs inside a Docker container and utilizes Symfony with Doctrine ORM.

---

## **How to Test**

### **1. Clone the Project**

```bash
git clone git@github.com:salmabenhadjali/usecase-roadsurfer.git
cd usecase-roadsurfer
```

### **2. Build the Docker Image**

```bash
docker build -t tturkowski/fruits-and-vegetables -f docker/Dockerfile .
```

### **3. Prepare the Environment**

```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables composer install
```

### **4. Create the Database**

```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables bin/console doctrine:migrations:migrate
```

### **5. Run Unit Tests**

```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables bin/phpunit
```

### **6. Start the Server**

```bash
docker run -it -w/app -v$(pwd):/app -p8080:8080 tturkowski/fruits-and-vegetables php -S 0.0.0.0:8080 -t /app/public
```

---

## **API Endpoints**

### **1. List & Filter Items**

**Endpoint:**

```http
GET /api/list/{type}
```

**Example Request:**

```bash
curl -X GET "http://127.0.0.1:8080/api/list/fruit?name=a&unit=g&maxWeight=10000"
```

**Query Parameters:**

| Parameter   | Type   | Required | Description                                  |
| ----------- | ------ | -------- | -------------------------------------------- |
| `type`      | string | ✅       | Collection type (`fruit` or `vegetable`)     |
| `name`      | string | ❌       | Filters results by name (e.g., `name=Apple`) |
| `unit`      | string | ❌       | Weight unit (`kg` or `g`), default is `g`    |
| `minWeight` | int    | ❌       | Filters results by minimum weight            |
| `maxWeight` | int    | ❌       | Filters results by maximum weight            |

**Example Response:**

```json
[
  {
    "id": 1,
    "name": "Apples",
    "weight": 5000,
    "unit": "g"
  }
]
```

### **2. Add an Item**

**Endpoint:**

```http
POST /api/add
```

**Example Request:**

```bash
curl -X POST http://127.0.0.1:8080/api/add \
     -H "Content-Type: application/json" \
     -d '{"type": "fruit", "name": "Apple", "quantity": 200, "unit": "kg"}'
```

**Request Body:**

```json
{
  "type": "fruit",
  "name": "Apple",
  "quantity": 200,
  "unit": "kg"
}
```

**Example Response:**

```json
{
  "message": "Item added successfully"
}
```

---

## **Database Schema**

### **Fruits Table (`fruits`)**

| Column   | Type   | Constraints                 |
| -------- | ------ | --------------------------- |
| `id`     | int    | Primary Key, Auto-Increment |
| `name`   | string | Not Null                    |
| `weight` | int    | Not Null (Stored in grams)  |

### **Vegetables Table (`vegetables`)**

| Column   | Type   | Constraints                 |
| -------- | ------ | --------------------------- |
| `id`     | int    | Primary Key, Auto-Increment |
| `name`   | string | Not Null                    |
| `weight` | int    | Not Null (Stored in grams)  |

---

## **Validation Rules**

- `type` must be either **`fruit`** or **`vegetable`**.
- `name` **must not be empty**.
- `quantity` **must be a positive integer**.
- `unit` must be either **`kg`** or **`g`**.

---

### 🎉 **Now You Are Ready to Use the Fruits and Vegetables API!**
