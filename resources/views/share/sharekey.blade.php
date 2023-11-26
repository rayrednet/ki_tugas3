<x-app-layout>
    <div class="row d-flex flex-column">
        <p class="fs-4 text-dark my-3">Bagikan Key</p>
        <hr>
        <div class="container">
            @if(isset($key))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Key anda!</strong>
                <p id="keyText" class="text-break my-1 fs-6">{{ $key }}</p>
                <button type="button" class="btn btn-secondary btn-sm mt-2" onclick="copyToClipboard()">Copy Key</button>
                @if($daftar_kontak["contact"] == "whatsapp")
                    <button onclick="shareViaWhatsApp('{{ $daftar_kontak["address"] }}')" class="btn btn-success btn-sm mt-2">WhatsApp</button>
                @elseif ($daftar_kontak["contact"] == "email")
                    <button onclick="shareViaEmail('{{ $daftar_kontak["address"] }}')" class="btn btn-danger btn-sm mt-2">Email</button>
                @elseif ($daftar_kontak["contact"] == "telegram")
                    <button onclick="shareViaTelegram('{{ $daftar_kontak["address"] }}')" class="btn btn-primary btn-sm mt-2">Telegram</button>

                @endif
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('share.index') }}" method="get">
                @csrf
                @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> {{ $errors->all()[0] }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
            </form>
        </div>
    </div>
    
    <script>
        function copyToClipboard() {
            // Membuat text area sementara
            var textArea = document.createElement("textarea");
            // Mengambil teks dari elemen yang menampilkan key
            textArea.value = document.getElementById("keyText").innerText;
            document.body.appendChild(textArea);
            textArea.select();
            textArea.setSelectionRange(0, 99999); // Untuk mobile view

            navigator.clipboard.writeText(textArea.value).then(function() {
                console.log('Key successfully copied to clipboard');
                var alertBox = document.querySelector('.alert strong');
                if (alertBox) {
                    alertBox.textContent = 'Key berhasil dicopy!';
                }
            }).catch(function(error) {
                console.error('Error copying key to clipboard', error);
            });

            document.body.removeChild(textArea);
        }

        function shareViaWhatsApp(address) {
            var key = document.getElementById("keyText").innerText;
            var phoneNumber = address; 
            var whatsappUrl = "https://wa.me/" + phoneNumber + "?text=" + encodeURIComponent("Your custom message: " + key);
            window.open(whatsappUrl, '_blank');
        }

        function shareViaEmail(address) {
            var key = document.getElementById("keyText").innerText;
            var emailAddress = address;
            var mailtoUrl = "mailto:" + emailAddress + "?subject=Sharing Key&body=" + encodeURIComponent(key);
            window.location.href = mailtoUrl;
        }

        function shareViaTelegram(address) {
            var key = document.getElementById("keyText").innerText;
            var telegramID = address;
            var telegramUrl = "https://t.me/" + telegramID + "?text=" + encodeURIComponent(key); // Ganti `location.href` dengan URL yang ingin Anda bagikan
            window.open(telegramUrl, '_blank');
        }

        // function shareViaTelegram() {
        //     var key = document.getElementById("keyText").innerText;
        //     var botToken = '6723940472:AAFrOt7NXzB1YaqAl8U8pqYOkBScp02W8-Y';
        //     var chatId = '828971035';
        //     var text = "Your custom message: " + encodeURIComponent(key);

        //     var telegramUrl = `https://api.telegram.org/bot${botToken}/sendMessage?chat_id=${chatId}&text=${encodeURIComponent(text)}`;

        //     fetch(telegramUrl, { method: 'POST' })
        //         .then(response => response.json())
        //         .then(data => {
        //             console.log("Message sent!", data);
        //         })
        //         .catch(error => {
        //             console.error("Error sending message:", error);
        //         });
        // }

    </script>

</x-app-layout>
