<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Immortal Live</title>
  <style>
    /* Font Faces */
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff2") format("woff2");
      font-weight: 400;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff2") format("woff2");
      font-weight: 600;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff2") format("woff2");
      font-weight: 400;
    }
    
    /* General styles */
    body {
      background-color: #000;
      color: white;
      margin: 0;
      font-family: "Calps Sans", sans-serif;
    }

    .page-content {
      text-align: center;
      padding: 10px 2px; 
      /* asal nya adalah 60px 20px*/
      color: white;
      font-family: "Calps Sans", sans-serif;
    }

    .title {
      font-size: 3rem;
      font-weight: bold;
      color: #ffffff;
      font-family: "ESL Legend", sans-serif;
      z-index: 1;
    }

    .immortal {
      font-size: 20rem;
      font-weight: bold;
      color: #ffffff;
      font-family: "ESL Legend", sans-serif;
      z-index: 2;
      position: relative;
    }

    .dynamic-text {
      display: inline-block;
      margin-top: 0px;
    }

    .headline {
      font-size: 4rem;
      font-weight: bold;
      color: white;
      /* margin-top: 20px; */
      font-family: "ESL Legend", sans-serif;
      z-index: 0;
    }

    .images {
      position: absolute; /* Position statues absolutely within the parent */
      top: 1; 
      left: 0;
      right: 0;
      bottom: 0;
      z-index: 3; /* Place statues behind the text */
      display: flex;
      justify-content: center;
      gap: 0px;
    }

    .statue {
      width: 300px;
      max-width: 100%;
      height: auto;
      border-radius: 5px;
      transition: transform 0.3s ease, opacity 0.3s ease;
      margin-bottom: 30px;
    }

    /* Hover effect for statues */
    .statue:hover {
      transform: scale(1.05);
      opacity: 0.8;
    }

    .tagline {
      font-size: 2rem;
      margin-top: 40px;
      font-weight: 500;
      color: #A8A8A8;
      font-family: "Calps Sans", sans-serif;
      z-index: 4;
    }

    #huruf {
      color: #000000;
    }

    @media (prefers-color-scheme: dark) {
      #huruf {
        color: #ffffff;
      }
    }

    /* Responsive styles */
    @media (max-width: 768px) {
      .title {
        font-size: 2rem;
      }
      .immortal {
        font-size: 8rem;
      }
      .headline {
        font-size: 2rem;
      }
      .tagline {
        font-size: 1.5rem;
        margin-top: 20px;
      }
      .images {
        flex-direction: column;
        align-items: center;
        gap: 10px;
      }
      .statue {
        width: 80%;
      }
    }

    @media (max-width: 480px) {
      .title {
        font-size: 1.5rem;
      }
      .immortal {
        font-size: 6rem;
      }
      .tagline {
        font-size: 1.2rem;
      }
      .images {
        gap: 5px;
      }
    }

  </style>
</head>
<body>

  <main id="content" class="site-main bg-white dark:bg-gray-900">
    <div class="page-content">
      <div class="header">
        <!-- "LIVE" word -->
        <h2 id="huruf" class="title">
          <span class="live">L</span>
          <span class="live">I</span>
          <span class="live">V</span>
          <span class="live">E</span>
        </h2>
        <!-- "IMMORTAL" word -->
        <h1 class="dynamic-text headline">
          <span id="huruf" class="immortal">I</span>
          <span id="huruf" class="immortal">M</span>
          <span id="huruf" class="immortal">M</span>
          <span id="huruf" class="immortal">O</span>
          <span id="huruf" class="immortal">R</span>
          <span id="huruf" class="immortal">T</span>
          <span id="huruf" class="immortal">A</span>
          <span id="huruf" class="immortal">L</span>
          
          <!-- Statues and other content -->
          <span class="images">
            <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-HenryG-768.png" alt="Statue 1" class="statue statue-left">
            <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-Natalia-768.png" alt="Statue 2" class="statue statue-center">
            <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-Twistzz-768.png" alt="Statue 3" class="statue statue-right">
          </span>
        </h1>
        <h1 class="tagline" id="huruf" >MAKING ESPORTS TO BREED LEGENDS</h1>
      </div>
    </div>
  </main>
<!--baru-->  

</body>
</html>










{{-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>..</title>
  <style>
    /* Font Faces */
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff2") format("woff2");
      font-weight: 400;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff2") format("woff2");
      font-weight: 600;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-lightitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-lightitalic-webfont.woff2") format("woff2");
      font-weight: 300;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff2") format("woff2");
      font-weight: 400;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-italic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-italic-webfont.woff2") format("woff2");
      font-weight: 400;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-medium-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-medium-webfont.woff2") format("woff2");
      font-weight: 500;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-mediumitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-mediumitalic-webfont.woff2") format("woff2");
      font-weight: 500;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bold-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bold-webfont.woff2") format("woff2");
      font-weight: 600;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bolditalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bolditalic-webfont.woff2") format("woff2");
      font-weight: 600;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-black-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-black-webfont.woff2") format("woff2");
      font-weight: 800;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-blackitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-blackitalic-webfont.woff2") format("woff2");
      font-weight: 800;
      font-style: italic;
    }

    /* General styles */
    .page-content {
      text-align: center;
      padding: 60px 20px;
      color: white;
      font-family: "Calps Sans", sans-serif;
    }

    .title {
      font-size: 3rem;
      font-weight: bold;
      color: #ffffff;
      /* animation: fadeInDown 1s ease-out; */
      font-family: "ESL Legend", sans-serif;
    }

    .immortal {
      font-size: 20rem;
      font-weight: bold;
      color: #ffffff;
      /* animation: fadeInDown 1s ease-out; */
      font-family: "ESL Legend", sans-serif;
    }

    .headline {
      font-size: 4rem;
      font-weight: bold;
      color: white;
      margin-top: 20px;
      animation: slideIn 1s ease-out;
      font-family: "ESL Legend", sans-serif;
    }

    .dynamic-text {
      display: inline-block;
      margin-top: 10px;
    }

    .images {
      position: relative;
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 30px 0;
    }

    .statue {
      width: 300px;
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      transition: transform 0.3s ease, opacity 0.3s ease;
    }

    /* Hover effect for statues */
    .statue:hover {
      transform: scale(1.05);
      opacity: 0.8;
    }

    .logo {
      width: 120px;
      position: absolute;
      top: -70px;
    }

    .tagline {
      font-size: 2rem;
      margin-top: 40px;
      font-weight: 500;
      animation: zoomInUp 1s ease-out;
      color: #A8A8A8;
      font-family: "Calps Sans", sans-serif;
    }

    #huruf {
      color: #000000;
    }

    @media (prefers-color-scheme: dark) {
      #huruf {
        color: #ffffff;
      }
    }

    /* Keyframe animations */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @keyframes zoomInUp {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    @keyframes randomEffect {
      0% { transform: translate(0, 0) rotate(0deg); }
      25% { transform: translate(-3px, 3px) rotate(-2deg); }
      50% { transform: translate(3px, -3px) rotate(2deg); }
      75% { transform: translate(-2px, 2px) rotate(-1deg); }
      100% { transform: translate(0, 0) rotate(0deg); }
    }

    .letter {
      display: inline-block;
      animation: randomEffect 2s infinite ease-in-out;
      font-size: 6vw;
      font-family: "ESL Legend", sans-serif;
    }

    /* .letter:nth-child(1) { animation-delay: 0s; }
    .letter:nth-child(2) { animation-delay: 0.1s; }
    .letter:nth-child(3) { animation-delay: 0.2s; }
    .letter:nth-child(4) { animation-delay: 0.3s; }
    .letter:nth-child(5) { animation-delay: 0.4s; } */

    /* General styles */
.page-content {
  text-align: center;
  padding: 60px 20px;
  color: white;
  font-family: "Calps Sans", sans-serif;
}

/* Adjust font sizes and layout for smaller screens */
@media (max-width: 768px) {
  .title {
    font-size: 2rem;
  }

  .immortal {
    font-size: 8rem; /* Reduce the font size */
  }

  .headline {
    font-size: 2rem;
  }

  .tagline {
    font-size: 1.5rem;
    margin-top: 20px;
  }

  /* Center the images in a column layout */
  .images {
    flex-direction: column;
    align-items: center;
    gap: 10px;
  }

  /* Resize the images */
  .statue {
    width: 80%; /* Adjust size to fit smaller screens */
  }
}

@media (max-width: 480px) {
  .title {
    font-size: 1.5rem;
  }

  .immortal {
    font-size: 6rem;
  }

  .tagline {
    font-size: 1.2rem;
  }

  /* Further adjust the layout for very small screens */
  .images {
    gap: 5px;
  }
}

  </style>
</head>
<body>

<main id="content" class="site-main bg-white dark:bg-gray-900">
  <div class="page-content">
    <div class="header">
      <!-- "LIVE" word -->
      <h2 id="huruf" class="title">
        <span class="live">L</span>
        <span class="live">I</span>
        <span class="live">V</span>
        <span class="live">E</span>
      </h2>
      <!-- "IMMORTAL" word -->
      <h1 id="huruf" class="dynamic-text" class="headline slideIn">
        <span class="dynamic-text flex">
          <span class="immortal">I</span>
          <span class="immortal">M</span>
          <span class="immortal">M</span>
          <span class="immortal">O</span>
          <span class="immortal">R</span>
          <span class="immortal">T</span>
          <span class="immortal">A</span>
          <span class="immortal">L</span>
        </span>
      </h1>
      <!-- Statues and other content -->
      <div class="images">
        <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-HenryG-768.png" alt="Statue 1" class="statue statue-left">
        <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-Natalia-768.png" alt="Statue 2" class="statue statue-center">
        <img src="https://esl.com/wp-content/uploads/2024/08/3D-ESL-STATUES-Twistzz-768.png" alt="Statue 3" class="statue statue-right">
      </div>
      <h1 class="tagline zoomInUp" class="tagline">MAKING ESPORTS TO BREED LEGENDS</h1>
    </div>
  </div>
</main>

</body>
</html> --}}



































{{-- -- -------------------------------------- --}}

{{-- <!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>..</title> --}}
  {{-- <style>
    /* Font Faces */
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-light.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-regular.woff2") format("woff2");
      font-weight: 400;
    }
    @font-face {
      font-family: "ESL Legend";
      src: url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/legend/v2/legend-bold.woff2") format("woff2");
      font-weight: 600;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-light-webfont.woff2") format("woff2");
      font-weight: 300;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-lightitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-lightitalic-webfont.woff2") format("woff2");
      font-weight: 300;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-regular-webfont.woff2") format("woff2");
      font-weight: 400;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-italic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-italic-webfont.woff2") format("woff2");
      font-weight: 400;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-medium-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-medium-webfont.woff2") format("woff2");
      font-weight: 500;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-mediumitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-mediumitalic-webfont.woff2") format("woff2");
      font-weight: 500;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bold-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bold-webfont.woff2") format("woff2");
      font-weight: 600;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bolditalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-bolditalic-webfont.woff2") format("woff2");
      font-weight: 600;
      font-style: italic;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-black-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-black-webfont.woff2") format("woff2");
      font-weight: 800;
    }
    @font-face {
      font-family: "Calps Sans";
      src: url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-blackitalic-webfont.woff") format("woff"),
           url("https://cdn1.api.esl.tv/fonts/calpssans/v1/calpssans-blackitalic-webfont.woff2") format("woff2");
      font-weight: 800;
      font-style: italic;
    }

    /* General styles */
    .page-content {
      text-align: center;
      padding: 60px 20px;
      color: white;
      font-family: "Calps Sans", sans-serif;
    }

    .title {
      font-size: 3rem;
      font-weight: bold;
      color: #ffffff;
      animation: fadeInDown 1s ease-out;
      font-family: "ESL Legend", sans-serif;
    }

    .headline {
      font-size: 4rem;
      font-weight: bold;
      color: white;
      margin-top: 20px;
      animation: slideIn 1s ease-out;
      font-family: "ESL Legend", sans-serif;
    }

    .dynamic-text {
      display: inline-block;
      margin-top: 10px;
    }

    .images {
      position: relative;
      display: flex;
      justify-content: center;
      gap: 20px;
      margin: 30px 0;
    }

    .statue {
      width: 300px;
      max-width: 100%;
      height: auto;
      border-radius: 10px;
      transition: transform 0.3s ease, opacity 0.3s ease;
    }

    /* Hover effect for statues */
    .statue:hover {
      transform: scale(1.05);
      opacity: 0.8;
    }

    .logo {
      width: 120px;
      position: absolute;
      top: -70px;
    }

    .tagline {
      font-size: 2rem;
      margin-top: 40px;
      font-weight: 500;
      animation: zoomInUp 1s ease-out;
      color: #A8A8A8;
      font-family: "Calps Sans", sans-serif;
    }

    #huruf {
      color: #000000;
    }

    @media (prefers-color-scheme: dark) {
      #huruf {
        color: #ffffff;
      }
    }

    /* Keyframe animations */
    @keyframes fadeInDown {
      from { opacity: 0; transform: translateY(-30px); }
      to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideIn {
      from { transform: translateX(-100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }

    @keyframes zoomInUp {
      from { transform: scale(0.8); opacity: 0; }
      to { transform: scale(1); opacity: 1; }
    }

    @keyframes randomEffect {
      0% { transform: translate(0, 0) rotate(0deg); }
      25% { transform: translate(-3px, 3px) rotate(-2deg); }
      50% { transform: translate(3px, -3px) rotate(2deg); }
      75% { transform: translate(-2px, 2px) rotate(-1deg); }
      100% { transform: translate(0, 0) rotate(0deg); }
    }

    .letter {
      display: inline-block;
      animation: randomEffect 2s infinite ease-in-out;
      font-size: 6vw;
      font-family: "ESL Legend", sans-serif;
    }

    .letter:nth-child(1) { animation-delay: 0s; }
    .letter:nth-child(2) { animation-delay: 0.1s; }
    .letter:nth-child(3) { animation-delay: 0.2s; }
    .letter:nth-child(4) { animation-delay: 0.3s; }
    .letter:nth-child(5) { animation-delay: 0.4s; }
  </style>
</head>
<body class="page-content">
  <div class="title">My Custom Title</div>
  <div class="headline">Custom Headline</div>
  <div class="dynamic-text">Dynamic Text</div>
  <div class="images">
    <img src="statue1.jpg" class="statue" alt="Statue 1">
    <img src="statue2.jpg" class="statue" alt="Statue 2">
  </div>
  <div class="tagline">Tagline Here</div>
</body>
</html> --}}
