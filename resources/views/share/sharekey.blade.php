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
                <!-- Modal Share Key -->
                <div class="modal fade" id="shareKeyModal" tabindex="-1" aria-labelledby="shareKeyModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="shareKeyModalLabel">Share Key</h5>
                                <button type="button" class="btn-close me-2 mt-1" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <button onclick="shareViaWhatsApp()" class="btn btn-success btn-sm mt-2">WhatsApp</button>
                                <button onclick="shareViaEmail()" class="btn btn-danger btn-sm mt-2">Email</button>
                                <button onclick="shareViaTwitter()" class="btn btn-dark btn-sm mt-2">Twitter</button>
                                <button onclick="shareViaTelegram()" class="btn btn-primary btn-sm mt-2">Telegram</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-success btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#shareKeyModal">
                    Share Key
                </button>
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
                <div class="form-outline mb-4">
                    <label class="form-label" for="nama">ID User Tujuan Enkripsi</label>
                    <input type="text" id="user_id" name="user_id" class="form-control" placeholder="Tuliskan ID user yang akan anda berikan key anda..." required />
                </div>
                @if(isset($usernameTujuan))
                <p>Username Tujuan: {{ $usernameTujuan }}</p>
                @endif
                <div class="justify-content-center d-flex">
                    <button type="submit" class="btn btn-primary btn-block mb-4 px-4">Enkripsi Key!</button>
                </div>
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