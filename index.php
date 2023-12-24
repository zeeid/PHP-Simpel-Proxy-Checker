<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proxy Input</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#loading").hide();
            $("form").submit(function (event) {
                event.preventDefault();
                var proxyAndPort = $("#proxyAndPort").val();
                var server = $("#server").val();
                var timeout = $("#timeout").val(); // Mengambil nilai timeout

                // Split the proxy string into an array using newline as the separator
                var proxies = proxyAndPort.split('\n');

                // Trim each proxy to remove leading and trailing whitespaces
                proxies = proxies.map(function (proxy) {
                    return proxy.trim();
                });

                // Array URL target berdasarkan server yang dipilih
                var urls = [];
                if (server === 'google') {
                    urls.push('https://www.google.com/humans.txt');
                } else if (server === 'ouo') {
                    urls.push('https://ouo.io/v3/js/script.js');
                }

                // Working proxies
                var workingProxies = [];
                $("#loading").show();

                // Eksekusi cURL secara asinkron untuk setiap proxy dan URL
                $.each(urls, function (index, url) {
                    // Loop through each proxy
                    $.each(proxies, function (proxyIndex, proxy) {
                        $.ajax({
                            url: 'process.php',
                            type: 'POST',
                            data: {
                                proxyAndPort: proxy,
                                server: server,
                                url: url,
                                timeout: timeout // Mengirimkan nilai timeout ke server
                            },
                            success: function (response) {
                                if (response.includes('Berhasil')) {
                                    workingProxies.push(proxy);
                                }
                            },
                            error: function () {
                                // Handle errors if needed
                            },
                            complete: function () {
                                // Setelah selesai, tambahkan working proxies ke textarea
                                $("#workingProxies").val(workingProxies.join('\n'));

                                // Tampilkan jumlah proxy yang berhasil
                                $("#numWorkingProxies").text(workingProxies.length);

                                // Sembunyikan loading setelah selesai pengecekan
                                if (index === urls.length - 1 && proxyIndex === proxies.length - 1) {
                                    $("#loading").hide();
                                }
                            }
                        });
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Input Proxy dan Port</h1>
        <form>
            <div class="form-group">
                <label for="proxyAndPort">Proxy dan Port:</label>
                <textarea class="form-control" name="proxyAndPort" id="proxyAndPort" rows="4" cols="50"></textarea>
            </div>

            <div class="form-group">
                <label for="server">Pilih Server:</label>
                <select class="form-control" name="server" id="server">
                    <option value="google">Google</option>
                    <option value="ouo">Ouo</option>
                </select>
            </div>

            <!-- Tambahkan elemen input untuk memilih timeout -->
            <div class="form-group">
                <label for="timeout">Pilih Timeout (detik):</label>
                <select class="form-control" name="timeout" id="timeout">
                    <?php
                    // Menambahkan opsi timeout kelipatan 5 dari 5 hingga 30
                    for ($i = 5; $i <= 30; $i += 5) {
                        echo "<option value=\"$i\">$i</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Submit</button>
        </form>

        <div id="loading" class="mt-3" style="display: none;">
            <p>Loading...</p>
        </div>

        <h2 class="mt-4">Working Proxy (Total: <span id="numWorkingProxies">0</span>):</h2>
        <textarea class="form-control" id="workingProxies" rows="4" cols="50" readonly></textarea>
    </div>
</body>
</html>
