<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ROH Clothing | Under Construction</title>

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      min-height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
      background: #0b0b0b;
      color: #ffffff;
      font-family: Arial, Helvetica, sans-serif;
      padding: 20px;
    }

    .wrapper {
      width: 100%;
      max-width: 700px;
      text-align: center;
    }

    .brand {
      font-size: 52px;
      font-weight: 900;
      letter-spacing: 10px;
      margin-bottom: 18px;
    }

    .line {
      width: 65px;
      height: 2px;
      background: #ffffff;
      margin: 0 auto 35px;
    }

    h1 {
      font-size: clamp(32px, 6vw, 58px);
      text-transform: uppercase;
      letter-spacing: 3px;
      margin-bottom: 20px;
    }

    p {
      max-width: 520px;
      margin: 0 auto 35px;
      color: #999;
      line-height: 1.8;
      font-size: 16px;
    }

    .construction-box {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 14px 25px;
      border: 1px solid #292929;
      border-radius: 50px;
      background: #121212;
    }

    .dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      background: #f5c542;
      animation: pulse 1.3s infinite;
    }

    @keyframes pulse {
      0%, 100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: .35;
        transform: scale(.8);
      }
    }

    .construction-box span {
      font-size: 14px;
      letter-spacing: 1px;
      text-transform: uppercase;
      color: #ddd;
    }

    .footer {
      margin-top: 55px;
      color: #555;
      font-size: 13px;
    }

    @media (max-width: 600px) {
      .brand {
        font-size: 38px;
        letter-spacing: 7px;
      }

      h1 {
        letter-spacing: 2px;
      }
    }
  </style>
</head>

<body>

  <main class="wrapper">

    <div class="brand">ROH</div>

    <div class="line"></div>

    <h1>Under Construction</h1>

    <p>
      We're currently working on something exciting.
      Our new clothing experience is coming soon.
    </p>

    <div class="construction-box">
      <div class="dot"></div>
      <span>Website Under Construction</span>
    </div>

    <div class="footer">
      © 2026 ROH Clothing. All Rights Reserved.
    </div>

  </main>

</body>
</html>