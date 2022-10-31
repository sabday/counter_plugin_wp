<?php

    global $wpdb;
    $tablename = $wpdb->prefix."counterlinks";

    // Update counter
    if(isset($_POST['submit'])){

        $counter_number = $_POST['counter_number'];
        $counter_number_db = "";
        $id = 1;

        if(!empty($counter_number)){
            $wpdb->query(
                $wpdb->prepare( "UPDATE $tablename SET counter = %s WHERE id = %d", $counter_number, $id )
            );
        }
    }
    //Get number
    $check_data = $wpdb->get_results("SELECT * FROM  " .$tablename." WHERE id =  1");
    if(count($check_data) > 0){
        foreach($check_data as $entry){
            $counter_number_db = $entry->counter;
        }
    }else{
        echo "Not Found";
    }
?>
    <div class="wrap">
        <h1>Counter Links</h1>
        <h2>This plugin allows you to set a variable number of internal links.</h2>
        <h3>Specify in the field the minimum number of links that you want to display in posts.</h3>
        <form method='post' action=''>
            <table>
                <tr>
                    <td>Minimum number of links: </td>
                    <td><input type='text' name='counter_number' placeholder='Input number links' value="<?php echo $counter_number_db; ?>"></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td><input type='submit' name='submit' value='Save'></td>
                </tr>
            </table>
        </form>
    </div>