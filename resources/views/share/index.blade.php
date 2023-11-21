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
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <button onclick="shareViaWhatsApp()" class="btn btn-success btn-sm mt-2">WhatsApp</button>
                                <button onclick="shareViaEmail()" class="btn btn-primary btn-sm mt-2">Email</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-info btn-sm mt-2" data-bs-toggle="modal" data-bs-target="#shareKeyModal">
                    Share Key
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            @endif
            <form action="{{ route('share.show') }}" method="get">
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
            var whatsappUrl = "https://wa.me/?text=" + encodeURIComponent(key);
            window.open(whatsappUrl, '_blank');
        }

        function shareViaEmail() {
            var key = document.getElementById("keyText").innerText;
            var mailtoUrl = "mailto:?subject=Sharing Key&body=" + encodeURIComponent(key);
            window.location.href = mailtoUrl;
        }
    </script>

</x-app-layout>
