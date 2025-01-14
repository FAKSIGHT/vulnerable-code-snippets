const snowflakesContainer = document.createElement('div');
snowflakesContainer.className = 'snowflakes';
snowflakesContainer.setAttribute('aria-hidden', 'true');
document.body.appendChild(snowflakesContainer);

function createSnowflake() {
  const flake = document.createElement('span');
  flake.className = 'snowflake';
  flake.textContent = Math.random() < 0.33 ? '❄' : Math.random() < 0.5 ? '❅' : '❆';
  flake.style.left = Math.random() * 100 + '%';
  flake.style.animationDuration = Math.random() * 3 + 3 + 's';
  flake.style.fontSize = Math.random() * 15 + 10 + 'px';
  flake.style.opacity = Math.random() * 0.7 + 0.3;
  snowflakesContainer.appendChild(flake);

  setTimeout(() => {
    flake.remove();
  }, 6000);
}

setInterval(createSnowflake, 10); 
