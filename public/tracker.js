(function () {
  const payload = {
    url: window.location.href,
    title: document.title,
    referrer: document.referrer,
    screen: {
      width: window.innerWidth,
      height: window.innerHeight,
    },
    html_hash: btoa(document.documentElement.innerHTML).substring(0, 32),
    events: [],
  };

  // Track click events
  document.addEventListener("click", function (e) {
    payload.events.push({
      type: "click",
      x: e.pageX,
      y: e.pageY,
      timestamp: Date.now(),
    });
  });

  // Send data before unload
  window.addEventListener("beforeunload", function () {
    navigator.sendBeacon("http://localhost:8000/track", JSON.stringify(payload));
  });
})();
