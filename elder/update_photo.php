<?php
/*! \file

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

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $device_id = $mysqli->escape_string($request->param('device_id'));
    $name = $mysqli->escape_string($request->param('name'));
    $remarks = $mysqli->escape_string($request->param('remarks'));

    // error checking
    if (is_empty(trim($device_id)))      $service->flash("Please enter your device_id.", 'error');
    if (is_empty(trim($name)))      $service->flash("Please enter your subject's name.", 'error');
    if (is_empty(trim($remarks)))      $service->flash("Please enter your subject's remarks.", 'error');


    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        // get user_id
        $user_id = $user_id_from_device_id($mysqli, $device_id);



        $sql_query = "UPDATE `photo` WHERE `user_id` = ? SET `name` = ?, `remarks` = ?";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("iss", $user_id, $name, $remarks);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Photo details updated", 'success');
                $return['status'] = 0;
                $return['message'] = $service->flashes('success');
            } else {
                $service->flash("Failed to insert data to database: " . $stmt->error, 'error');
                $return['status'] = -1;
                $return['message'] = $service->flashes('error');
            }
            $stmt->close();
        } else {
            $service->flash("SQL statement error ", 'error');
            $return['status'] = -1;
            $return['message'] = $service->flashes('error');
        }
    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
    }
    return json_encode($return);
});