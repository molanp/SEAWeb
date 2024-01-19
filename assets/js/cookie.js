const cookie = {
  get: function (name) {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i].trim();
      if (cookie.startsWith(name + "=")) {
        return cookie.substring(name.length + 1);
      }
    }
    return null;
  },

  set: function (name, value, max_age = 0, path = "/") {
    let cookie = `${name}=${encodeURIComponent(value)}`;
    if (max_age !== 0) {
      cookie += `;max-age=${max_age}`;
    }
    cookie += `;path=${path}`;
    document.cookie = cookie;
    return true;
  },
  

  modify: function (name, value, expires, path = "/") {
    this.remove(name);
    this.set(name, value, expires, path);
    return true;
  },

  remove: function (name) {
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    return true;
  },

  clear: function () {
    const cookies = document.cookie.split(";");
    for (let i = 0; i < cookies.length; i++) {
      const cookie = cookies[i];
      const eqPos = cookie.indexOf("=");
      const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
      document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    }
    return true;
  }
};
