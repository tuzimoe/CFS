<?php
//marathon/marathinInfo
function marathon_marathonInfo() {
  return json_decode('[{"event_id":52,"point_name":"\u6247\u5b50","point_icon_asset":"assets\/flash\/ui\/live\/img\/e_icon_01.png","event_point":0,"total_event_point":0,"event_scenario":{"progress":1,"event_scenario_status":[]}}]');
}

function marathon_top() {
  return json_decode('{"event_status":{"total_event_point":0,"event_rank":0}}',true);
}
?>