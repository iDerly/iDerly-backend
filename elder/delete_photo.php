<?php
/*! \file

### Delete photo

```
POST /elder/delete_photo
```

#### Parameters
- `id`: **photo id**

#### Return
- `status`: 0 on success, -1 otherwise
- `message`: array of success/error messages

*/
$this->respond('POST', '/?', function ($request, $response, $service, $app) {
    $mysqli = $app->db;
    $id = $mysqli->escape_string($request->param('id'));

    // error checking
    if (is_empty(trim($id)))      $service->flash("Please enter your id.", 'error');


    $error_msg = $service->flashes('error');

    if (is_empty($error_msg)) {
        $sql_query = "DELETE FROM `photo` WHERE `id` = ?";
        $stmt = $mysqli->prepare($sql_query);
        if ($stmt) {
            $stmt->bind_param("i", $id);
            $res = $stmt->execute();
            if ($res) {
                $service->flash("Photo deleted", 'success');
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
