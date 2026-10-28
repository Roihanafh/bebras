<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Kartu Member - {{ $anggota->formatted_id_member }}</title>
    <style>
        @page {
            margin: 0;
            size: 1016pt 638pt;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            margin: 0;
            padding: 0;
            width: 1016pt;
            height: 638pt;
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            position: relative;
            background-color: #00adef;
            overflow: hidden;
        }
        .bg-card {
            position: absolute;
            top: 0;
            left: 0;
            width: 1016pt;
            height: 638pt;
            z-index: 0;
        }
        .qr-wrapper {
            position: absolute;
            top: 55pt;
            right: 38pt;
            width: 195pt;
            height: 195pt;
            background: #ffffff;
            padding: 8pt;
            border-radius: 6pt;
            text-align: center;
            z-index: 10;
        }
        .qr-wrapper img {
            width: 179pt;
            height: 179pt;
            display: block;
            margin: 0 auto;
        }
        .id-member {
            position: absolute;
            top: 280pt;
            right: 38pt;
            width: 211pt;
            text-align: center;
            color: #ffffff;
            font-size: 24pt;
            font-weight: 900;
            letter-spacing: 2pt;
            font-family: Arial, Helvetica, sans-serif;
            line-height: 1.1;
            z-index: 10;
        }
        .nama-member {
            position: absolute;
            top: 356pt;
            right: 38pt;
            width: 650pt;
            text-align: right;
            color: #000000;
            font-size: 46pt;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.05;
            letter-spacing: -0.5pt;
            font-family: 'Arial Black', Arial, Helvetica, sans-serif;
            z-index: 10;
        }
        .nama-biro {
            position: absolute;
            top: 502pt;
            right: 38pt;
            width: 650pt;
            text-align: right;
            color: #ffffff;
            font-size: 30pt;
            font-weight: 800;
            text-transform: uppercase;
            line-height: 1.1;
            font-family: Arial, Helvetica, sans-serif;
            z-index: 10;
        }
        .exp-date {
            position: absolute;
            top: 581pt;
            right: 38pt;
            width: 400pt;
            text-align: right;
            color: #000000;
            font-size: 20pt;
            font-weight: 600;
            font-family: Arial, Helvetica, sans-serif;
            z-index: 10;
        }
    </style>
</head>
<body>
    <img class="bg-card" src="{{ $templateBase64 }}" alt="Background Template">
    <div class="qr-wrapper">
        <img src="{{ $qrBase64 }}" alt="QR Code">
    </div>
    <div class="id-member">{{ $anggota->formatted_id_member }}</div>
    <div class="nama-member">{{ $anggota->nama }}</div>
    <div class="nama-biro">{{ $anggota->nama_biro }}</div>
    <div class="exp-date">{{ $anggota->formatted_expiry_date }}</div>
</body>
</html>
