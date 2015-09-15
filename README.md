# iDerly-backend

## REST API Entry Point
```
https://iderly.kenrick95.org/
```
OR
```
https://iderly-kenrick95.rhcloud.com/
```

## Actions available
### Authenticate
```
POST /elder/auth
```

#### Parameters
* `device_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of success/error messages

### Login
```
POST /caregiver/login
```

#### Parameters
* `email`
* `password`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of error messages; or `session_id` on success, please save this `session_id` locally as it will be used for authentication for other method.

### Logout
```
POST /caregiver/logout
```

#### Parameters
* `session_id`, returned at login

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of success/error messages
