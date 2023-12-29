let currentZIndex = 1;

function bringToFront(event) {
    const windowElement = event.currentTarget;
    currentZIndex += 1;
    windowElement.style.zIndex = currentZIndex;
}

window.addEventListener("blur", restoreZIndex);

function restoreZIndex() {
    const windowElements = document.querySelectorAll(".window");
    for (const windowElement of windowElements) {
        windowElement.style.removeProperty("z-index");
    }
}

// 窗口管理器
const windowManager = {
    windows: [],

    createWindow: function (title, content, id) {
        const newWindow = document.createElement("div");
        newWindow.className = "window hidden";
        newWindow.onclick = (event) => {
            bringToFront(event);
        };


        const titleBar = document.createElement("div");
        titleBar.className = "titleBar";

        const titleElement = document.createElement("span");
        titleElement.innerHTML = title;

        const controls = document.createElement("div");
        controls.className = "controls";

        const closeBtn = document.createElement("button");
        closeBtn.innerHTML = "X";

        const contentElement = document.createElement("div");
        contentElement.className = "content";
        contentElement.id = id
        contentElement.innerHTML = content;

        titleBar.appendChild(titleElement);
        controls.appendChild(closeBtn);
        titleBar.appendChild(controls);
        newWindow.appendChild(titleBar);
        newWindow.appendChild(contentElement);

        this.windows.push(newWindow);
        document.getElementById("data").appendChild(newWindow);

        let isDragging = false;
        let offset = { x: 0, y: 0 };

        titleBar.addEventListener("mousedown", startDragging);
        titleBar.addEventListener("touchstart", startDragging, { passive: true });

        function startDragging(e) {
            if (e.type === "mousedown") {
                isDragging = true;
                offset = { x: e.offsetX, y: e.offsetY };
            } else if (e.type === "touchstart") {
                const touch = e.touches[0];
                isDragging = true;
                offset = { x: touch.clientX - newWindow.offsetLeft, y: touch.clientY - newWindow.offsetTop };
            }
        }

        window.addEventListener("mousemove", dragWindow);
        window.addEventListener("touchmove", dragWindow, { passive: true });

        function dragWindow(e) {
            if (isDragging) {
                e.preventDefault();

                if (e.type === "mousemove") {
                    newWindow.style.left = e.pageX - offset.x + "px";
                    newWindow.style.top = e.pageY - offset.y + "px";
                } else if (e.type === "touchmove") {
                    const touch = e.touches[0];
                    newWindow.style.left = touch.clientX - offset.x + "px";
                    newWindow.style.top = touch.clientY - offset.y + "px";
                }
            }
        }

        window.addEventListener("mouseup", stopDragging);
        window.addEventListener("touchend", stopDragging);

        function stopDragging() {
            isDragging = false;
        }

        closeBtn.addEventListener("click", () => {
            this.closeWindow(newWindow);
        });

        return newWindow;
    },

    openWindow: function (title, content, id = "") {
        const newWindow = this.createWindow(title, content, id);
        newWindow.classList.remove("hidden");
        return newWindow;
    },

    closeWindow: function (window) {
        const index = this.windows.indexOf(window);
        if (index !== -1) {
            this.windows.splice(index, 1);
            document.getElementById("data").removeChild(window);
        }
    }
};
