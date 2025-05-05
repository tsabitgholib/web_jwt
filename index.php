<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JWT Encoder/Decoder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" />
    <style>
        textarea::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        textarea::-webkit-scrollbar-thumb {
            background-color: #cbd5e1;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-white text-black font-sans p-6">
    <main class="max-w-[1100px] mx-auto flex flex-col gap-8">

        <section class="flex-1 flex flex-col gap-4">
            <div class="flex items-center justify-between mb-2">
                <h2 class="text-lg font-normal text-black flex items-center gap-2">
                    <span class="font-extrabold">Encode</span>
                </h2>
            </div>

            <!--header-->
            <div class="border border-gray-300 rounded text-[11px]">
                <div class="bg-gray-100 border-b border-gray-300 px-3 py-1 font-extrabold uppercase text-gray-600 select-none">
                    Header: <span class="font-normal">Algorithm & Token Type</span>
                </div>
                <textarea 
                    id="headerInput"
                    rows="4"
                    class="w-full border border-gray-300 rounded px-3 py-2 resize-y font-mono text-[13px] leading-[1.3] text-pink-500"
                    spellcheck="false"
                    oninput="updateJWT()"
                >{
  "typ": "JWT",
  "alg": "HS256"
}</textarea>
            </div>

            <!--payload-->
            <div class="border border-gray-300 rounded text-[11px]">
                <div class="bg-gray-100 border-b border-gray-300 px-3 py-1 font-extrabold uppercase text-gray-600 select-none">
                    Payload: <span class="font-normal">Data</span>
                </div>
                <textarea 
                    id="payloadInput" 
                    rows="8" 
                    class="w-full border border-gray-300 rounded px-3 py-2 resize-y font-mono text-[13px] leading-[1.3] text-pink-500"
                    spellcheck="false"
                    oninput="updateJWT()"
                    placeholder='{"username": "user123", "role": "admin"}'
                ></textarea>
            </div>

            <!--secret Key-->
            <div class="border border-gray-300 rounded text-[11px]">
                <div class="bg-gray-100 border-b border-gray-300 px-3 py-1 font-extrabold uppercase text-gray-600 select-none">
                    Secret Key
                </div>
                <input 
                    id="secretKeyInput" 
                    type="text" 
                    class="w-full border border-gray-300 rounded px-3 py-2 text-[13px] leading-[1.3]"
                    placeholder="Enter your secret key"
                    oninput="updateJWT()"
                />
            </div>
        </section>

        <section class="flex-1">
            <h2 class="text-lg font-normal text-black mb-1 flex items-center gap-2">
                <span class="font-extrabold">Decode</span>
            </h2>
            <textarea
                id="jwtInput"
                rows="12"
                class="w-full border border-gray-300 rounded px-3 py-2 resize-y font-mono text-[13px] leading-[1.3] text-pink-500"
                spellcheck="false"
                oninput="decodeJWT()"
                placeholder="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."
            ></textarea>
        </section>
    </main>

    <script>
        function decodeJWT() {
            const jwtInput = document.getElementById('jwtInput').value;
            if (jwtInput) {
                fetch('jwt.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ jwt: jwtInput })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.payload) {
                        document.getElementById('headerInput').value = JSON.stringify(data.header, null, 2);
                        document.getElementById('payloadInput').value = JSON.stringify(data.payload, null, 2);
                    } else {
                        document.getElementById('headerInput').value = "Invalid JWT format.";
                        document.getElementById('payloadInput').value = '';
                    }
                })
                .catch(error => console.error('Error decoding JWT:', error));
            }
        }

        function updateJWT() {
            const payload = document.getElementById('payloadInput').value;
            const header = document.getElementById('headerInput').value;
            const secretKey = document.getElementById('secretKeyInput').value;

            fetch('jwt.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ payload: payload, header: header, secret: secretKey })
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('jwtInput').value = data.encodedJWT;
            })
            .catch(error => console.error('Error encoding JWT:', error));
        }
    </script>
</body>
</html>
