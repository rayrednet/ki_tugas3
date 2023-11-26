import $ from "jquery";

$(".btn-request").on("click", function () {
    let username = $(this).data("username").username;
    let kontak = $(this).data("kontak").contact;
    let tujuan = $(this).data("tujuan").address;

    $("#username").val(username);
    $("#kontak").val(kontak);
    $("#tujuan").val(tujuan);

});


function shareViaWhatsApp() {
    var key = document.getElementById("keyText").innerText;
    var phoneNumber = "6285101339177"; 
    var whatsappUrl = "https://wa.me/" + phoneNumber + "?text=" + encodeURIComponent("Your custom message: " + key);
    window.open(whatsappUrl, '_blank');
}

function shareViaEmail() {
    var key = document.getElementById("keyText").innerText;
    var mailtoUrl = "mailto:?subject=Sharing Key&body=" + encodeURIComponent(key);
    window.location.href = mailtoUrl;
}

function shareViaTelegram() {
    var key = document.getElementById("keyText").innerText;
    var telegramUrl = "https://t.me/share/url?url=" + encodeURIComponent(location.href) + "&text=" + encodeURIComponent(key); // Ganti `location.href` dengan URL yang ingin Anda bagikan
    window.open(telegramUrl, '_blank');
}