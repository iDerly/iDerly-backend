<?php
/*! \file

### Authenticate

```
POST /elder/auth
```

#### Parameters
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of error messages; if success, returns session_id

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $device_id = $mysqli->escape_string($request->param('device_id'));

    // error checking
    if (is_empty(trim($device_id)))
        $service->flash("Please enter your device_id.", 'error');


    $num_rows = 0;
    $sql_query = "SELECT * FROM `user` WHERE `device_id` = ?";
    $stmt = $mysqli->prepare($sql_query);
    if ($stmt) {
        $stmt->bind_param("s", $device_id);
        $res = $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;

        $stmt->close();
    }
    if ($num_rows === 1) {
        session_start();
        $_SESSION['auth'] = TRUE;
        $_SESSION['device_id'] = $device_id;

        $service->flash(session_id(), 'success');
        $return['status'] = 0;
        $return['message'] = $service->flashes('success');
        return json_encode($return);
        //$service->flash("Device_id already in use, please use another device_id.", 'error');
    }
    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "INSERT INTO user(`device_id`)
                    VALUES(?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("s", $device_id);
            $res = $stmt->execute();
            if ($res) {
                $_SESSION['auth'] = TRUE;
                $_SESSION['device_id'] = $device_id;

                $service->flash(session_id(), 'success');
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
