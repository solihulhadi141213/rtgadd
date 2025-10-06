//Notification First Time
$('#MenampilkanBelNotifikasi').load('_Partial/ReloadBelNotification.php');

//Reload Notification
$(document).ready(function() {
    function ReloadBelNotification() {
        $('#MenampilkanBelNotifikasi').load('_Partial/ReloadBelNotification.php');
    }
    // setInterval(ReloadBelNotification, 5000);
    setInterval(ReloadBelNotification, 5000);
});

//Kondisi Ketika Uraian Notifikasi Di Klik
$('#MenampilkanBelNotifikasi').click(function(){
    $('#MenampilkanNotificationList').html('<li class="dropdown-header">Loading...</li>');
    $('#MenampilkanNotificationList').load('_Partial/NotificationList.php');
});