# iDerly-backend
[Documentation in Doxygen format](https://rawgit.com/iDerly/iDerly-backend/master/docs/html/index.html)

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
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages

##### Example

```
{"status":0,"message":["User successfully registered."]}
```
OR
```
{"status":-1,"message":["Device_id already in use, please use another device_id."]}
```


### Login

```
POST /caregiver/login
```

#### Parameters
- `email`
- `password`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of error messages; or `session_id` on success, please save this `session_id` locally as it will be used for authentication for other method.


### Logout

```
POST /caregiver/logout
```

#### Parameters
- `session_id`, returned at login

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Register

```
POST /caregiver/register
```

#### Parameters
- `email`
- `password`
- `name`
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages



### Add elder

```
POST /caregiver/add_elder
```

#### Parameters
- `elder_device_id`
- `caregiver_device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Delete elder

```
POST /caregiver/delete_elder
```

#### Parameters
- `elder_device_id`
- `caregiver_device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Forgot password

```
POST /user/forgot
```

#### Parameters
* `device_id`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of error/success messages

### Reset password

```
GET /user/reset/[s:password_token]
```
Users are expected to access this URL from e-mail sent by `/user/forget`

#### Parameters
* `password_token`: Generated from `/user/forget`

#### Return
* `status`: 0 on success, -1 otherwise
* `message`: array of error/success messages



### Get list of elders under care of caregiver, with their (profile) photo

```
REQUEST /caregiver/view_caregiver_and_elder/[s:caregiver_device_id]
```

#### Parameters
- `caregiver_device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of error messages; or list of elder under care of caregiver with their details: [user_id, name, base-64 encoded image]
  - ***NOTE*** zero-th index indicates caregiver's details.


### Add photo

```
POST /elder/add_photo
```

#### Parameters
- `attachment`: base-64 encoded string of the photo
- `device_id`: who owns the photo
- `name`: name of person in photo (not user's name)
- `remarks`: remarks of person in photo

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Update photo

```
POST /elder/update_photo
```

#### Parameters
- `device_id`: who owns the photo
- `name`: name of person in photo (not user's name)
- `remarks`: remarks of person in photo

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Delete photo

```
POST /elder/delete_photo
```

#### Parameters
- `id`: **photo id**

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages



### Get list of elders under care of caregiver, with their (profile) photo

```
REQUEST /caregiver/view_elder_photo/[s:caregiver_device_id]
```

#### Parameters
- `caregiver_device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of error messages; or list of elder under care of caregiver with its photos: [user_id, name, base-64 encoded image]


### View elder profile

```
REQUEST /elder/view/[s:device_id]
```

#### Parameters
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Update elder profile

```
POST /elder/update
```

#### Parameters
- `device_id`
- `attachment`: base-64 encoded string of the photo
- `name`: name of user

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


### Get photos stored by elders

```
REQUEST /elder/photos/[s:device_id]
```

#### Parameters
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of error messages; or list of (photo_id, base-64 encoded image, attachment, remarks, #appear, #correct)


### Add game_result

```
POST /game/add_result
```

#### Parameters
- `device_id`
- `score`
- `time_start` YYYY-MM-DD HH:mm:SS; `Y-M-D H:i:s`; 
- `time_end` YYYY-MM-DD HH:mm:SS; `Y-M-D H:i:s`; 
- `mode`: "classic" or "unlimited"

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages



### Increase photo statistics

```
POST /game/inc_photo_stats
```

#### Parameters
- `photo_id`
- `option`:
  - 0: increment number of appearance ONLY
  - 1: increment number of appearance AND correctness

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages
