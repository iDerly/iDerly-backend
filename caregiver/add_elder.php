<?php
/*! \file

### Add elder

```
POST /caregiver/add_elder
```

#### Parameters
- `elder_device_id`
- `caregiver_device_id`
- `elder_name`
- `elder_attachment`

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $elder_device_id = $mysqli->escape_string($request->param('elder_device_id'));
    $caregiver_device_id = $mysqli->escape_string($request->param('caregiver_device_id'));
    $elder_name = $mysqli->escape_string($request->param('elder_name'));
    $elder_attachment = $mysqli->escape_string($request->param('elder_attachment'));

    // error checking
    if (is_empty(trim($elder_device_id)))      $service->flash("Please enter your elder_device_id.", 'error');
    if (is_empty(trim($caregiver_device_id)))      $service->flash("Please enter your caregiver_device_id.", 'error');
    if (is_empty(trim($elder_name)))      $service->flash("Please enter your elder_name.", 'error');
    if (is_empty(trim($elder_attachment)))      $service->flash("Please enter your elder_attachment.", 'error');
    

    $num_rows = 0;
    $sql_query = "SELECT * FROM `user` WHERE `device_id` = ?";
    $stmt = $mysqli->prepare($sql_query);
    
    if ($stmt) {
        $stmt->bind_param("s", $elder_device_id);
        $res = $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
    }

    if ($num_rows === 0) {
        $sql_query = "INSERT INTO user(`device_id`, `name`, `attachment`) VALUES(?, ?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("sss", $elder_device_id, $elder_name, $elder_attachment);
            $res = $stmt->execute();
        }
    }



    $num_rows = 0;
    $sql_query = "SELECT * FROM `take_care`, `caregiver`, `user`
        WHERE `caregiver_id` = `caregiver`.`user_id`
        AND `caregiver`.`id` = ?
        AND `user_id` = `user`.`id`
        AND `user`.`id` = ?";
    $stmt = $mysqli->prepare($sql_query);
    if ($stmt) {
        $stmt->bind_param("ss", $caregiver_device_id, $elder_device_id);
        $res = $stmt->execute();

        $stmt->store_result();
        $num_rows = $stmt->num_rows;

        $stmt->close();
    }
    if ($num_rows === 1) {
        $service->flash("Relationship already exists.", 'error');
    }
    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        // get caregiver_id
        // get user_id
        $user_id = $user_id_from_device_id($mysqli, $caregiver_device_id);
        $caregiver_id = $user_id_from_device_id($mysqli, $caregiver_device_id);
        
        $sql_query = "INSERT INTO take_care(`caregiver_device_id`, `elder_device_id`)
                      VALUES(?, ?)";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("ii", $caregiver_device_id, $elder_device_id);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Elder successfully added for the care of caregiver.", 'success');
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
