import $ from "jquery";

$(".btn-request").on("click", function () {
    let username = $(this).data("user").username;
    let userId = $(this).data("user").id;

    $("#username").val(username);
    $("#user_id").val(userId);
});
