# API Documentation

## Base URL
```
http://localhost/api
```

## Authentication
Most endpoints require authentication using Laravel Sanctum. Include the token in the Authorization header:
```
Authorization: Bearer {token}
```

---

## Public Endpoints (No Authentication)

### Ping Test
```http
GET /api/ping
```
**Response:**
```json
{
  "message": "pong",
  "timestamp": "2026-02-12T10:00:00.000000Z",
  "status": "ok"
}
```

### Register
```http
POST /api/register
```
**Request Body:**
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "department": "IT",
  "phone": "+36 1 234 5678"
}
```
**Response:** `201 Created`
```json
{
  "message": "Registration successful",
  "user": {...},
  "access_token": "1|xyz...",
  "token_type": "Bearer"
}
```

### Login
```http
POST /api/login
```
**Request Body:**
```json
{
  "email": "admin@taskmanager.hu",
  "password": "admin123"
}
```
**Response:**
```json
{
  "message": "Login successful",
  "user": {...},
  "access_token": "2|abc...",
  "token_type": "Bearer"
}
```

---

## Authenticated Endpoints

### Logout
```http
POST /api/logout
```
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "message": "Logout successful"
}
```

### Get Current User
```http
GET /api/me
```
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
  "id": 1,
  "name": "Admin",
  "email": "admin@taskmanager.hu",
  "is_admin": true,
  ...
}
```

### Update Profile
```http
PUT /api/me
```
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "name": "Updated Name",
  "department": "HR",
  "phone": "+36 1 999 8888",
  "password": "newpassword123",
  "password_confirmation": "newpassword123"
}
```

### Get My Tasks
```http
GET /api/my-tasks
```
**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
[
  {
    "id": 1,
    "title": "Task Title",
    "description": "Task description",
    "priority": "high",
    "due_date": "2026-03-01",
    "status": "in_progress",
    "taskAssignments": [...]
  }
]
```

### Update Task Status
```http
PUT /api/my-tasks/{id}/status
```
**Headers:** `Authorization: Bearer {token}`

**Request Body:**
```json
{
  "status": "completed"
}
```
**Note:** Status values: `pending`, `in_progress`, `completed`, `cancelled`

---

## Admin Only Endpoints

All admin endpoints require `Authorization: Bearer {token}` header and the user must have `is_admin: true`.

### Admin Dashboard
```http
GET /api/admin/dashboard
```
**Response:**
```json
{
  "users_count": 10,
  "tasks_count": 15,
  "assignments_count": 25,
  "pending_tasks": 5,
  "completed_tasks": 8
}
```

---

## User Management (Admin)

### List All Users
```http
GET /api/admin/users
```
**Response:** Paginated list of users

### Create User
```http
POST /api/admin/users
```
**Request Body:**
```json
{
  "name": "Jane Doe",
  "email": "jane@example.com",
  "password": "password123",
  "department": "Sales",
  "phone": "+36 1 111 2222",
  "is_admin": false
}
```

### Get User
```http
GET /api/admin/users/{id}
```

### Update User
```http
PUT /api/admin/users/{id}
```
**Request Body:**
```json
{
  "name": "Updated Name",
  "email": "newemail@example.com",
  "department": "Marketing",
  "is_admin": true
}
```

### Soft Delete User
```http
DELETE /api/admin/users/{id}
```

### Force Delete User
```http
DELETE /api/admin/users/{id}/force
```

### Restore User
```http
POST /api/admin/users/{id}/restore
```

---

## Task Management (Admin)

### List All Tasks
```http
GET /api/admin/tasks
```
**Response:** Paginated list of tasks with assignments

### Create Task
```http
POST /api/admin/tasks
```
**Request Body:**
```json
{
  "title": "New Task",
  "description": "Task description here",
  "priority": "high",
  "due_date": "2026-03-15",
  "status": "pending"
}
```
**Note:**
- `priority`: `low`, `medium`, `high`
- `status`: `pending`, `in_progress`, `completed`, `cancelled`

### Get Task
```http
GET /api/admin/tasks/{id}
```

### Update Task
```http
PUT /api/admin/tasks/{id}
```
**Request Body:**
```json
{
  "title": "Updated Title",
  "status": "in_progress",
  "priority": "medium"
}
```

### Soft Delete Task
```http
DELETE /api/admin/tasks/{id}
```

### Force Delete Task
```http
DELETE /api/admin/tasks/{id}/force
```

### Restore Task
```http
POST /api/admin/tasks/{id}/restore
```

---

## Task Assignment Management (Admin)

### List All Assignments
```http
GET /api/admin/task-assignments
```

### Create Assignment
```http
POST /api/admin/task-assignments
```
**Request Body:**
```json
{
  "user_id": 2,
  "task_id": 5,
  "assigned_at": "2026-02-12T10:00:00Z"
}
```

### Get Assignment
```http
GET /api/admin/task-assignments/{id}
```

### Update Assignment
```http
PUT /api/admin/task-assignments/{id}
```
**Request Body:**
```json
{
  "completed_at": "2026-02-12T15:00:00Z"
}
```

### Delete Assignment
```http
DELETE /api/admin/task-assignments/{id}
```

### Assign Task to User
```http
POST /api/admin/tasks/{taskId}/assign
```
**Request Body:**
```json
{
  "user_id": 3
}
```

### Unassign Task from User
```http
DELETE /api/admin/tasks/{taskId}/unassign/{userId}
```

---

## Error Responses

### 401 Unauthorized
```json
{
  "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
  "message": "Unauthorized. Admin access required."
}
```

### 404 Not Found
```json
{
  "message": "Model not found"
}
```

### 422 Validation Error
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "email": ["The email field is required."]
  }
}
```

---

## Test Credentials

### Admin User
- Email: `admin@taskmanager.hu`
- Password: `admin123`

### Regular Users
- Password for all: `Jelszo12`
- 9 users with random emails

---

## Notes

1. All dates should be in ISO 8601 format
2. Pagination returns 15 items per page by default
3. Soft deletes are used - deleted records can be restored
4. Task status changes automatically update assignment completion
5. Users cannot delete themselves
6. Regular users can only see and update their assigned tasks
