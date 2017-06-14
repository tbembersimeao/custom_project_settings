<?php
require_once "cps.php";

class cps_lib {
  var $tableName;
  var $conn;

  function cps_lib ($conn_object) {
    $this->conn = $conn_object;
    $this->tableName = 'uf_project_settings';

    
    if ($this->isTableExists()) {
      echo 'Table '. $this->tableName . ' exists in db. ' . $dbname . '<br>';
    } else {
      echo 'Table ', $this->tableName . ' does not exist in db.' . '<br>';
      echo 'Creating the table.' . PHP_EOL;
      $this->createTable();
    }
  }

  function isTableExists() {

    $sql = "SHOW TABLES LIKE '$this->tableName'";

    if ($result=$this->conn->query($sql)) {
        if ($result->num_rows > 0) {
          return true;
        }
    }
    return false;
  }

  function createTable() {
    
    $sql ="CREATE TABLE IF NOT EXISTS $this->tableName (
    
    id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    project_id INT(6) NOT NULL,
    attribute VARCHAR(50) NOT NULL,
    value TEXT,
    created_by VARCHAR(50),
    created_on DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_by VARCHAR(50),
    updated_on DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY ukey_project_id_attr_key (project_id, attribute),
    FOREIGN KEY (project_id)
            REFERENCES redcap_projects(project_id)
            ON DELETE CASCADE
    );" ;

    if ($this->conn->query($sql)) {
      echo "Created $this->tableName Table.";
    } else {
      echo "Table creation failed.";
    }
  }

  function save($records) {
    foreach ($records as $item) {
      if ($item->id == NULL) {
        $this->insertData($item);
      } else {
        $this->updateData($item);
      }
    }
  }

  function getDataByProjectId($project_id) {
    
    $sql = "SELECT id, project_id, attribute, value, created_by, updated_by, created_on, updated_on from $this->tableName where project_id = ?";

    if ($stmt=$this->conn->prepare($sql)) {
      $stmt->bind_param("i", $project_id);
      $stmt->execute();

      /* bind variables to prepared statement */
      $stmt->bind_result($col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8);

      $result = array();
      while ($stmt->fetch()) {
        //creating cps object 
        $row_obj = new cps();
        $row_obj->id = $col1;
        $row_obj->project_id = $col2;
        $row_obj->attribute = $col3;
        $row_obj->value = $col4;
        $row_obj->created_by = $col5;
        $row_obj->updated_by = $col6;
        $row_obj->created_on = $col7;
        $row_obj->updated_on = $col8;

        //collecting the objects into a array
        $result[] = $row_obj;
      }
      return $result;
    }
  }

  function getAttributeData($project_id, $attribute) {

    $sql = "SELECT value from $this->tableName where project_id = ? and attribute = ?";
    if ($stmt=$this->conn->prepare($sql)) {
      $stmt->bind_param("is", $project_id, $attribute);
      $stmt->execute();

      /* bind variables to prepared statement */
      $stmt->bind_result($col1);
      $stmt->fetch();

      return $col1;
    }
  }  

  function insertData($inputRecord) {
    $sql = "INSERT INTO $this->tableName (project_id, attribute, value, created_by) VALUES (?, ?, ?, ?)";    
    if ($stmt=$this->conn->prepare($sql)) {
      $stmt->bind_param("isss", $inputRecord->project_id, $inputRecord->attribute, $inputRecord->value, $inputRecord->created_by);
      if ($stmt->execute()) {
        echo "New record created successfully." . '<br>';
        return;
      }
    }
    echo "Insertion failed." . '<br>';
  }

  function updateData($inputRecord) {
    $sql = "UPDATE $this->tableName SET project_id= ?, attribute= ?, 
        value= ?, created_by= ?, updated_by = ? WHERE id= ?";
    if ($stmt=$this->conn->prepare($sql)) {
      $stmt->bind_param('issssi', $inputRecord->project_id, $inputRecord->attribute, 
          $inputRecord->value, $inputRecord->created_by, $inputRecord->updated_by, $inputRecord->id);
      if ($stmt->execute()) {
        echo "Record updated successfully with id " . $inputRecord->id . "<br>";
        return;
      }
    }
    echo "Update failed for id " . $inputRecord->id . "<br>";
  }

  function deleteData($id) {
    $sql = "DELETE FROM $this->tableName WHERE id= ?";
    if ($stmt=$this->conn->prepare($sql)) {
      $stmt->bind_param('i', $id);
      if ($stmt->execute()) {
        echo "Record deleted successfully with id " . $inputRecord->id . "<br>";
        return;
      }
    }
    echo "Delete failed for id " . $inputRecord->id . "<br>";
  }

}

?>