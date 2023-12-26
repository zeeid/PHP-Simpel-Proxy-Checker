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
                $("#btnsubmit").prop('disabled', true);

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

                // Inisialisasi currentTask
                var currentTask = 0;
                var taskForLoading = 0;

                // Fungsi untuk memproses blok proxy
                function processProxyBlock(startIndex) {
                    var endIndex = Math.min(startIndex + 10, proxies.length);

                    // Ambil blok proxy
                    var proxyBlock = proxies.slice(startIndex, endIndex);
                    var totalTasks = proxies.length * urls.length;

                    // Eksekusi cURL secara asinkron untuk setiap proxy dan URL
                    $.each(proxyBlock, function (proxyIndex, proxy) {
                        // Loop through each URL
                        $.each(urls, function (urlIndex, url) {
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

                                    // Setelah selesai, tambahkan working proxies ke textarea
                                    $("#workingProxies").val(workingProxies.join('\n'));

                                    // Tampilkan jumlah proxy yang berhasil
                                    $("#numWorkingProxies").text(workingProxies.length);

                                    // Tambahkan 1 ke currentTask setiap kali tugas selesai
                                    currentTask++;
                                    taskForLoading++;


                                    // Hitung persentase berdasarkan jumlah proxy dan URL yang sudah diperiksa
                                    
                                    var percentage = (taskForLoading / totalTasks) * 100;

                                    // Perbarui nilai persentase pada progress bar
                                    $("#progressBar").css("width", percentage + "%").attr("aria-valuenow", percentage).text(Math.round(percentage) + "%");

                                    // Jika semua tugas selesai, perbarui persentase secara keseluruhan
                                    if (taskForLoading === totalTasks) {
                                        // Perbarui nilai persentase pada progress bar
                                        $("#progressBar").css("width", "100%").attr("aria-valuenow", 100);
                                        $("#loading").hide();
                                        $("#btnsubmit").prop('disabled', false);
                                    }
                                },
                                error: function () {
                                    // Handle errors if needed
                                },
                                complete: function () {
                                    // Jika belum mencapai akhir proxies, panggil lagi fungsi untuk memproses blok berikutnya

                                    if (currentTask == 10) {
                                        if (currentTask < totalTasks) {
                                            currentTask = 0
                                            processProxyBlock(endIndex);
                                        }
                                    }
                                }
                            });
                        });
                    });
                }

                // Mulai pemrosesan blok proxy dari awal (indeks 0)
                processProxyBlock(0);
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
                        $selected = ($i === 10) ? 'selected' : ''; // Tambahkan kondisi untuk memeriksa nilai 30
                        echo "<option value=\"$i\" $selected>$i</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" id="btnsubmit" class="btn btn-primary">Submit</button>
        </form>

        <div class="container mt-5">
            <div id="loading" class="mt-3" style="display: none;">
                <p>Loading...</p>
            </div>

            <!-- Tambahkan elemen progress -->
            <div id="progressContainer">
                <div class="progress">
                    <div id="progressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
            </div>
        </div>

        <h2 class="mt-4">Working Proxy (Total: <span id="numWorkingProxies">0</span>):</h2>
        <textarea class="form-control" id="workingProxies" rows="4" cols="50" readonly></textarea>
    </div>
</body>
</html>
