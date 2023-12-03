let paramIndex = 0;

function addParamRow() {
  const newRow = $("<tr></tr>");

  const paramNameCell = $("<td></td>");
  const paramNameInput = $("<input>").attr({
    "class": "mdui-text-field",
    "type": "text",
    "name": `paramName-${paramIndex}`,
    "placeholder": "参数名"
  });
  paramNameCell.append(paramNameInput);

  const paramValueCell = $("<td></td>");
  const paramValueInput = $("<input>").attr({
    "class": "mdui-text-field",
    "type": "text",
    "name": `paramValue-${paramIndex}`,
    "placeholder": "值"
  });
  paramValueCell.append(paramValueInput);

  const actionCell = $("<td></td>");
  const deleteButton = $("<mdui-button-icon></mdui-button-icon>").attr({
    "style": "color: red"
  }).attr("icon","delete").click(function () {
    newRow.remove();
  });
  actionCell.append(deleteButton);

  newRow.append(paramNameCell, paramValueCell, actionCell);
  $("#paramsTable").append(newRow);

  paramIndex++;
}

function sendRequest() {
  const methodSelect = $("#methodSelect");

  const params = {};
  $("#paramsTable tr").each(function (index, row) {
    const paramName = $(row).find("td:eq(0) input").val();
    const paramValue = $(row).find("td:eq(1) input").val();
    if (paramName && paramValue) {
      params[paramName] = paramValue;
    }
  });

  const currentPath = window.location.pathname.match(/\/docs(.*)\//)[1];
  const url = `${currentPath.startsWith("/api") ? "" : "/api"}${currentPath}`; // 拼接 URL

  $.ajax({
    url: url,
    method: methodSelect.val(),
    data: params,
    success: function (response, status, xhr) {
      var contentType = xhr.getResponseHeader("content-type");
      if (contentType.startsWith("image/")) {
        var container = $("#responseTEXT");
        container.html("暂不支持查看图片");
      } else {
        renderResponseCard(response);
      }
    },
    error: function (xhr, status, error) {
      const response = xhr.responseText || "请求失败";
      try {
        const jsonResponse = JSON.parse(response);
        renderResponseCard(jsonResponse);
      } catch (e) {
        renderResponseCard(response);
      }
    }
  });
}

function syntaxHighlight(json) {
  json = json.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
    var cls = "number";
    if (/^"/.test(match)) {
      if (/:$/.test(match)) {
        cls = "key";
      } else {
        cls = "string";
      }
    } else if (/true|false/.test(match)) {
      cls = "boolean";
    } else if (/null/.test(match)) {
      cls = "null";
    }
    return "<span class='" + cls + "'>" + match + "</span>";
  });
}

function renderResponseCard(response) {
  const responseText = $("#responseTEXT");
  responseText.html(syntaxHighlight(JSON.stringify(response, undefined, 4)));
}
