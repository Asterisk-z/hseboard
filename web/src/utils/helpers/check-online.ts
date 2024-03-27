
const checkOnlineStatus = async () => {
  try {
    const online = await fetch('1pixel.png');
    return online.status >= 200 && online.status < 300;
  } catch (err) {
    return false
  }
}

setInterval(async () => {
  const result = await checkOnlineStatus();
  const statusDisplay = document.getElementById('status');
  statusDisplay.textContent = result ? "Online" : "Offline";
}, 300)

window.addEventListener("load", async (event) => {
  const statusDisplay = document.getElementById("status");
  statusDisplay.textContent = (await checkOnlineStatus())
    ? "Online"
    : "OFFline";
});