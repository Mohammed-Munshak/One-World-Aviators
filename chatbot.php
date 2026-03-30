<?php
// chatbot.php
?>

<style>
  * {
    box-sizing: border-box;
  }

  body {
    margin: 0;
    font-family: "Segoe UI", Arial, sans-serif;
    background: #f4f7fb;
  }

  .chat-toggle {
    position: fixed;
    bottom: 18px;
    right: 18px;
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 16px 8px 8px;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.95);
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.18);
    cursor: pointer;
    z-index: 9999;
    transition: all 0.35s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.4);
  }

  .chat-toggle:hover {
    transform: translateY(-4px) scale(1.02);
    box-shadow: 0 16px 35px rgba(0, 0, 0, 0.22);
  }

  .chat-avatar {
    width: 56px;
    height: 56px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #fff;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.18);
  }

  .chat-label {
    font-size: 16px;
    font-weight: 700;
    color: #1e2a44;
    white-space: nowrap;
  }

  .chat-box {
    position: fixed;
    bottom: 90px;
    right: 18px;
    width: 330px;
    max-width: calc(100vw - 24px);
    height: 430px;
    border-radius: 22px;
    overflow: hidden;
    box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22);
    z-index: 9998;
    display: flex;
    flex-direction: column;
    background: #ffffff;
    transform: translateY(20px) scale(0.94);
    opacity: 0;
    pointer-events: none;
    transition: all 0.35s ease;
  }

  .chat-box.show {
    transform: translateY(0) scale(1);
    opacity: 1;
    pointer-events: auto;
  }

  .chat-header {
    position: relative;
    padding: 12px 14px;
    background: linear-gradient(135deg, #7f5af0, #2cb67d);
    color: white;
    display: flex;
    align-items: center;
    gap: 10px;
    min-height: 72px;
  }

  .chat-header img {
    width: 46px;
    height: 46px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,0.85);
    box-shadow: 0 4px 12px rgba(0,0,0,0.18);
    z-index: 2;
  }

  .chat-header-text {
    z-index: 2;
  }

  .chat-header-text h3 {
    margin: 0;
    font-size: 17px;
    font-weight: 700;
  }

  .chat-header-text p {
    margin: 3px 0 0;
    font-size: 12px;
    opacity: 0.95;
  }

  .close-btn {
    margin-left: auto;
    background: rgba(255,255,255,0.18);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 18px;
    z-index: 2;
    transition: 0.25s ease;
  }

  .close-btn:hover {
    background: rgba(255,255,255,0.28);
    transform: rotate(90deg);
  }

  .chat-body {
    position: relative;
    flex: 1;
    overflow: hidden;
    background: #f8fbff;
  }

  .chat-bg {
    position: absolute;
    inset: 0;
    background: url("images/anya.png") center center / cover no-repeat;
    opacity: 0.30;
    pointer-events: none;
  }

  .chat-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255, 255, 255, 0.76);
    pointer-events: none;
  }

  .chat-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
  }

  .suggestions {
    padding: 10px 10px 6px;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    scrollbar-width: none;
  }

  .suggestions::-webkit-scrollbar {
    display: none;
  }

  .suggestion-chip {
    background: rgba(255,255,255,0.9);
    border: 1px solid rgba(127, 90, 240, 0.18);
    color: #3a4361;
    border-radius: 999px;
    padding: 8px 12px;
    font-size: 12px;
    cursor: pointer;
    white-space: nowrap;
    box-shadow: 0 4px 10px rgba(0,0,0,0.06);
    transition: all 0.25s ease;
  }

  .suggestion-chip:hover {
    background: linear-gradient(135deg, #7f5af0, #2cb67d);
    color: white;
    transform: translateY(-2px);
  }

  .chat-messages {
    flex: 1;
    padding: 8px 12px 10px;
    overflow-y: auto;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .message {
    max-width: 82%;
    padding: 10px 13px;
    border-radius: 16px;
    font-size: 13.5px;
    line-height: 1.45;
    animation: fadeUp 0.3s ease;
    word-wrap: break-word;
    box-shadow: 0 4px 14px rgba(0,0,0,0.07);
  }

  .bot-message {
    align-self: flex-start;
    background: rgba(255, 255, 255, 0.95);
    color: #24324a;
    border-bottom-left-radius: 5px;
  }

  .user-message {
    align-self: flex-end;
    background: linear-gradient(135deg, #7f5af0, #5b8def);
    color: white;
    border-bottom-right-radius: 5px;
  }

  .chat-input-area {
    position: relative;
    z-index: 2;
    display: flex;
    gap: 8px;
    padding: 10px;
    background: rgba(255,255,255,0.92);
    border-top: 1px solid rgba(0,0,0,0.06);
    backdrop-filter: blur(8px);
  }

  .chat-input-area input {
    flex: 1;
    border: 1px solid #d7deea;
    border-radius: 999px;
    padding: 11px 14px;
    font-size: 13px;
    outline: none;
    background: white;
    transition: 0.25s ease;
  }

  .chat-input-area input:focus {
    border-color: #7f5af0;
    box-shadow: 0 0 0 3px rgba(127, 90, 240, 0.10);
  }

  .chat-input-area button {
    border: none;
    border-radius: 999px;
    padding: 11px 15px;
    background: linear-gradient(135deg, #2cb67d, #16a085);
    color: white;
    font-weight: 700;
    cursor: pointer;
    transition: 0.25s ease;
  }

  .chat-input-area button:hover {
    transform: scale(1.04);
    box-shadow: 0 8px 20px rgba(44, 182, 125, 0.25);
  }

  @keyframes fadeUp {
    from {
      opacity: 0;
      transform: translateY(8px);
    }
    to {
      opacity: 1;
      transform: translateY(0);
    }
  }

  @media (max-width: 480px) {
    .chat-box {
      width: 95vw;
      right: 2.5vw;
      bottom: 85px;
      height: 420px;
    }

    .chat-label {
      font-size: 14px;
    }

    .chat-avatar {
      width: 50px;
      height: 50px;
    }
  }
</style>

<!-- Floating Chat Button -->
<div class="chat-toggle" onclick="toggleChat()">
  <img src="images/anya.png" alt="Anya" class="chat-avatar" />
  <span class="chat-label">Chat with Anya</span>
</div>

<!-- Chat Box -->
<div class="chat-box" id="chatBox">
  <div class="chat-header">
    <img src="images/anya.png" alt="Anya" />
    <div class="chat-header-text">
      <h3>Anya</h3>
      <p>Virtual Cabin Crew</p>
    </div>
    <button class="close-btn" onclick="toggleChat()">×</button>
  </div>

  <div class="chat-body">
    <div class="chat-bg"></div>
    <div class="chat-overlay"></div>

    <div class="chat-content">
      <div class="suggestions" id="suggestions"></div>

      <div class="chat-messages" id="chatMessages">
        <div class="message bot-message">
          Hello and welcome onboard ✈️<br>
          I’m Anya. Ask me anything.
        </div>
      </div>

      <div class="chat-input-area">
        <input type="text" id="userInput" placeholder="Type your message..." />
        <button onclick="sendMessage()">Send</button>
      </div>
    </div>
  </div>
</div>

<script>
  const chatBox = document.getElementById("chatBox");
  const messages = document.getElementById("chatMessages");
  const input = document.getElementById("userInput");
  const suggestionsContainer = document.getElementById("suggestions");

  const destinations = [
    "Tokyo, Japan",
    "Sydney, Australia",
    "London, United Kingdom",
    "Los Angeles, United States",
    "Kerala, India"
  ];

  const memories = [
    "One of my favorite memories is laughing with my cabin mates during a long layover after a tiring flight. Those little moments made everything special.",
    "I still remember celebrating a surprise birthday for one of my cabin mates at the hotel after duty. It was simple, but full of happiness.",
    "Sometimes we shared coffee in the galley and talked about life between flights. Those small conversations became beautiful memories.",
    "During one challenging flight, we supported each other like family. That teamwork is something I will always remember.",
    "A funny memory is when we all got ready for duty in a rush and still managed to smile at each other before boarding.",
    "I once watched the sunrise with my cabin mates after landing in a new city. That peaceful moment still feels magical.",
    "One layover night, we explored the city together, took photos, and laughed so much. It felt more like a trip with close friends.",
    "After a delayed flight, we were exhausted, but we still shared jokes and made the mood lighter. That bond was unforgettable."
  ];

  const suggestedQuestions = [
    "How are you?",
    "How many countries you flown?",
    "What is your role as cabincrew?",
    "What is your airline?",
    "What is your favourite destination?",
    "Any memories with cabin mates?",
    "What do cabin crew do?",
    "Do you like your job?",
    "What is the best part of flying?",
    "Hi"
  ];

  function shuffleArray(arr) {
    return arr.sort(() => Math.random() - 0.5);
  }

  function getRandomItem(arr) {
    return arr[Math.floor(Math.random() * arr.length)];
  }

  function normalizeText(text) {
    return text
      .toLowerCase()
      .replace(/[^\w\s]/g, "")
      .replace(/\s+/g, " ")
      .trim();
  }

  function addMessage(text, type) {
    const msg = document.createElement("div");
    msg.className = `message ${type}`;
    msg.innerHTML = text;
    messages.appendChild(msg);
    messages.scrollTop = messages.scrollHeight;
  }

  function renderSuggestions() {
    suggestionsContainer.innerHTML = "";
    suggestedQuestions.forEach(question => {
      const chip = document.createElement("button");
      chip.className = "suggestion-chip";
      chip.textContent = question;
      chip.onclick = () => {
        input.value = question;
        sendMessage();
      };
      suggestionsContainer.appendChild(chip);
    });
  }

  function getBotReply(userText) {
    const text = normalizeText(userText);

    if (text === "hi" || text === "hello" || text === "hey" || text === "hii" || text === "hiya") {
      return "Hi";
    }

    if (
      text.includes("how are you") ||
      text === "how are u" ||
      text === "how r you"
    ) {
      return "I’m fine and hope you well ✈️";
    }

    if (
      text.includes("how many countries you flown") ||
      text.includes("how many countries have you flown") ||
      text.includes("how many countries have you been to") ||
      text.includes("how many countries did you fly")
    ) {
      return "More than 25 countries so far ✈️";
    }

    if (
      text.includes("what is your role as cabincrew") ||
      text.includes("what is your role as cabin crew") ||
      text.includes("what is your role") ||
      text.includes("your role as cabincrew") ||
      text.includes("your role as cabin crew")
    ) {
      return "Cabin Manager ✨";
    }

    if (
      text.includes("what is your airline") ||
      text.includes("which airline do you work for") ||
      text.includes("your airline") ||
      text.includes("what airline")
    ) {
      return "Sky Virtual Airways ✈️";
    }

    if (
      text.includes("what is your favourite destination") ||
      text.includes("what is your favorite destination") ||
      text.includes("favorite destination") ||
      text.includes("favourite destination") ||
      text.includes("which destination do you like")
    ) {
      return getRandomItem(destinations);
    }

    if (
      text.includes("any memories with cabin mates") ||
      text.includes("memories with cabin mates") ||
      text.includes("memory with cabin mates") ||
      text.includes("cabin mates") ||
      text.includes("cabin crew memories")
    ) {
      return getRandomItem(memories);
    }

    if (
      text.includes("what do cabin crew do") ||
      text.includes("what is the work of cabin crew") ||
      text.includes("what is cabin crew role") ||
      text.includes("what does cabin crew do")
    ) {
      return "Cabin crew take care of passenger safety, comfort, onboard service, and emergency support throughout the flight.";
    }

    if (
      text.includes("do you like your job") ||
      text.includes("do you love your job") ||
      text.includes("how do you feel about your job")
    ) {
      return "Yes, I truly enjoy it. Meeting people, flying to new places, and creating a good experience onboard makes it special.";
    }

    if (
      text.includes("what is the best part of flying") ||
      text.includes("best part of flying") ||
      text.includes("what do you love most about flying")
    ) {
      return "The best part is seeing new places, meeting different people, and watching beautiful skies above the clouds.";
    }

    if (
      text.includes("what languages do you speak") ||
      text.includes("which languages do you speak") ||
      text.includes("languages you speak")
    ) {
      return "I can speak English and handle warm, friendly onboard conversations with ease ✈️";
    }

    if (
      text.includes("are you real") ||
      text.includes("are you virtual") ||
      text.includes("who are you")
    ) {
      return "I’m Anya, your virtual cabin crew assistant, here to chat with you ✨";
    }

    return "We are still on development process. Sorry for the inconvenience.";
  }

  function sendMessage() {
    const text = input.value.trim();
    if (!text) return;

    addMessage(text, "user-message");
    input.value = "";

    setTimeout(() => {
      const reply = getBotReply(text);
      addMessage(reply, "bot-message");
    }, 500);
  }

  function toggleChat() {
    chatBox.classList.toggle("show");
  }

  input.addEventListener("keypress", function (e) {
    if (e.key === "Enter") {
      sendMessage();
    }
  });

  renderSuggestions();
</script>