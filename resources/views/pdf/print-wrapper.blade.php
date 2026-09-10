<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        * { box-sizing: border-box; }
        html, body { margin: 0; height: 100%; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f6f3f0; }
        #toolbar {
            display: flex; align-items: center; justify-content: space-between;
            padding: 10px 16px; background: #ffffff; border-bottom: 1px solid #e5e3de;
        }
        #toolbar span { font-size: 13px; color: #6b7068; }
        #printBtn {
            padding: 8px 18px; border: none; border-radius: 8px;
            background: #7d523c; color: #fff; font-size: 14px; font-weight: 600;
            cursor: pointer;
        }
        #printBtn:hover { background: #986a52; }
        iframe { display: block; width: 100%; height: calc(100% - 45px); border: none; }
        @media print {
            #toolbar { display: none !important; }
            iframe { height: 100% !important; }
        }
    </style>
</head>
<body>
    <div id="toolbar">
        <span>{{ $title }}</span>
        <button id="printBtn" type="button">Print</button>
    </div>
    <iframe id="pdfFrame" src="{{ route('temp-pdf.show', $token) }}" title="{{ $title }}"></iframe>

    <script>
        document.getElementById('printBtn').addEventListener('click', function () {
            this.style.display = 'none';
            setTimeout(function () {
                var frame = document.getElementById('pdfFrame');
                try {
                    frame.contentWindow.focus();
                    frame.contentWindow.print();
                } catch (e) {
                    window.print();
                }
            }, 500);
        });
    </script>
</body>
</html>
