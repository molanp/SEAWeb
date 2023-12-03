// 获取Cookie
function getCookie(name) {
  const cookies = document.cookie.split(";");
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i].trim();
    if (cookie.startsWith(name + "=")) {
      return cookie.substring(name.length + 1);
    }
  }
  return null;
}

// 设置Cookie，expires单位分钟
function setCookie(name, value, expires = 0, path = "/") {
  let cookie = `${name}=${encodeURIComponent(value)}`;
  if (expires) {
    const date = new Date();
    date.setTime(date.getTime() + expires * 60 * 1000);
    cookie += `;expires=${date.toUTCString()}`;
  }
  cookie += `;path=${path}`;
  document.cookie = cookie;
}

// 修改Cookie
function modifyCookie(name, value, expires, path = "/") {
  deleteCookie(name);
  setCookie(name, value, expires, path);
}

// 删除Cookie
function deleteCookie(name) {
  document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
}

//清理Cookies
function clearCookies() {
  const cookies = document.cookie.split(";");
  for (let i = 0; i < cookies.length; i++) {
    const cookie = cookies[i];
    const eqPos = cookie.indexOf("=");
    const name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
  }
}