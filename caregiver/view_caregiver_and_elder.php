<?php
/*! \file

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

*/
$this->respond('/[s:caregiver_device_id]', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $user_id_from_device_id = $app->user_id_from_device_id;
    $caregiver_device_id = $mysqli->escape_string($request->param('caregiver_device_id'));

    // error checking
    if (is_empty(trim($caregiver_device_id)))     $service->flash("Please enter the caregiver_device_id.", 'error');    

    $error_msg = $service->flashes('error');
    $user_id = $user_id_from_device_id($mysqli, $caregiver_device_id);

    if (is_empty($error_msg)) {
        $sql_query = "SELECT `device_id`, `name`, `attachment`
            FROM `user`
            WHERE `device_id` = ?";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("i", $caregiver_device_id);
        $res = $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($device_id, $name, $attachment);

        $result = [];
        $stmt->fetch();

        array_push($result, array(
            "device_id" => $device_id,
            "name" => $name,
            "attachment" => $attachment
        ));
    

        $sql_query = "SELECT `user`.`device_id`, `user`.`name`, `user`.`attachment`
            FROM `take_care`, `user`, `user` AS `cuser`
            WHERE
                `cuser`.`device_id` = ? AND
                `take_care`.`user_id` = `user`.`id` AND
                `cuser`.`id` = `take_care`.`caregiver_id`
            LIMIT 0,100";
        $stmt = $mysqli->prepare($sql_query);
        $stmt->bind_param("i", $caregiver_device_id);
        $res = $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($device_id, $name, $attachment);

        //$result = [];
        while ($stmt->fetch()) {
            array_push($result, array(
                "device_id" => $device_id,
                "name" => $name,
                "attachment" => $attachment
            ));
        }


        // http://stackoverflow.com/a/6061602/917957
        // $img = base64_decode($attachment);
        // $f = finfo_open();

        // $mime_type = finfo_buffer($f, $img, FILEINFO_MIME_TYPE);

        // header('Content-Type: '. $mime_type);
        // return $img;
        $return['status'] = 0;
        $return['message'] = $result;
        return json_encode($return);

    } else {
        $return['status'] = -1;
        $return['message'] = $error_msg;
        return json_encode($return);
    }
    
});
