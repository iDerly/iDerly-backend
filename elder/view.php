<?php
/*! \file

### View elder profile

```
REQUEST /elder/view/[s:device_id]
```

#### Parameters
- `device_id`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages


#### Todo
- incorporate game result here (some stats like when game is last played, average score, hi score in each mode)
*/
$this->respond('/[s:device_id]', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $device_id = $mysqli->escape_string($request->param('device_id'));

    // error checking
    if (is_empty(trim($device_id)))
        $service->flash("Please enter your device_id.", 'error');

    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        // get user_id
        $user_id = $user_id_from_device_id($mysqli, $device_id);
    
        $sql_query = "SELECT `device_id`, `date_created`, `name`, `attachment` FROM `user` WHERE `id` = ?";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("i", $user_id);
            $res = $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($device_id, $date_created, $name, $attachment);
            $stmt->fetch();

            $return_msg = array(
                "device_id" => $device_id,
                "date_created" => $date_created,
                "name" => $name,
                "attachment" => $attachment
            );

            $stmt->close();
            $return['status'] = 0;
            $return['message'] = $return_msg;

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
