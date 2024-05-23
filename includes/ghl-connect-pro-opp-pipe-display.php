<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Process the form data after submission
    $selectedContactID = $_POST["contactsList_names"];//contactid
    $selectedPipelineID = $_POST["pipeline_name"];//pipeline id
    $selectedStageID = $_POST["pipeline_stage"];//stage id
    $OppName = $_POST["opp_name"]; //oppertunity name
    $OppVal= $_POST["opp_val"]; //oppertunity Value.
    $locID=get_option('ghlconnectpro_locationId');//location id.
    $selectedStatus = $_POST["status"];//Status
    $data = array(
		"pipelineId"     => $selectedPipelineID,
        "locationId"     => $locID,
        "name"           => $OppName,
        "pipelineStageId"=> $selectedStageID,
        "status"         =>$selectedStatus,
        "contactId"      => $selectedContactID,
        "monetaryValue"  => $OppVal
	);
    
    $send=ghlconnectpro_get_create_oppertunity($data);
    // if($send!=''){
    //     $type="success";
    //     $message="Pipeline created successfully";
    //     do_action('admin_notices', $message, $type);
    // }
    // else{
    //     $type="error";
    //     $message="Pipeline not created";
    //     do_action('admin_notices', $message, $type);
    // }

    
        
}

// Fetch pipelines using the API.
$pipelines = ghlconnectpro_get_pipelines();

// Initialize arrays to store pipeline information.
$pipeline_names = [];
$pipeline_ids = [];
$pipeline_stages = [];

// Ensure that pipelines are fetched and not null.
if (!empty($pipelines) && is_array($pipelines)) {
    foreach ($pipelines as $pipeline) {
        // Ensure the pipeline object has the necessary properties.
        if (isset($pipeline->id) && isset($pipeline->name)) {
            $pipeline_names[$pipeline->id] = $pipeline->name;
            $pipeline_ids[] = $pipeline->id;
    
            // Initialize the stages array for this pipeline.
            $stages = [];
            if (isset($pipeline->stages) && is_array($pipeline->stages)) {
                foreach ($pipeline->stages as $stage) {
                    // Ensure the stage object has the necessary properties.
                    if (isset($stage->id) && isset($stage->name)) {
                        $stages[$stage->id] = $stage->name;
                    }
                }
            }
            $pipeline_stages[$pipeline->id] = $stages;
        }
    }
}


$contactsLists = ghlconnectpro_get_contactsList();


?>
<div class="wrap main-con">
    <div class="ghl-header">
        <!-- Logo -->
        <div class="logo">
        <img src="<?php echo esc_url(plugins_url('images/ghlconnectpro-logo.png', __DIR__)); ?>" alt="GHLCONNECTPRO-Logo" />

        </div>
        
        <h1>GHL Connect for WooCommerce Pro</h1>
</div>
<div class="opp-pipe">
<form method="post">
    <table class="form-table">
        <tr>
            <th scope="row">Contact Name:</th>
            <td>
                <select name='contactsList_names' id='contactsList_names'>
                    <?php
                    foreach ($contactsLists as $contactList ) {
                        echo "<option value='$contactList->id'>$contactList->contactName</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">Opportunity Name:</th>
            <td><input class="oppertunity" type="text" name="opp_name"></td>
        </tr>
        <tr>
            <th scope="row">Pipeline Name:</th>
            <td>
                <select name='pipeline_name' id='pipeline_name' onchange='updateStages()'>
                    <?php
                    foreach ($pipeline_names as $id => $name) {
                        echo "<option value='$id'>$name</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">Stage Name:</th>
            <td>
                <select name='pipeline_stage' id='pipeline_stage'>
                    <?php
                    // Default to the first pipeline's stages
                    foreach ($pipeline_stages[$pipeline_ids[0]] as $id => $stage) {
                        echo "<option value='$id'>$stage</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">Status:</th>
            <td>
                <select name='status'>
                    <option value='open'>Open</option>
                    <option value='lost'>Lost</option>
                    <option value='won'>Won</option>
                    <option value='abandon'>Abandon</option>
                </select>
            </td>
        </tr>
        <tr>
            <th scope="row">Opportunity Value:</th>
            <td><input type="number" name="opp_val" placeholder="0"></td>
        </tr>
    </table>

    <?php wp_nonce_field('custom_form_nonce', 'custom_form_nonce'); ?>

    <p class="submit">
        <input type='submit' class='ghl_connect button' value='Create'>
    </p>
</form>
</div>




<script>
function updateStages() {
    var pipelineNameDropdown = document.getElementById("pipeline_name");
    var pipelineId = pipelineNameDropdown.value;
    var stages = <?php echo json_encode($pipeline_stages); ?>;
    var stageDropdown = document.getElementById("pipeline_stage");

    // Clear existing options
    stageDropdown.innerHTML = "";

    // Add options based on the selected pipeline
    for (var id in stages[pipelineId]) {
        var option = document.createElement("option");
        option.value = id;
        option.text = stages[pipelineId][id];
        stageDropdown.add(option);
    }
}

</script>

