let paramIndex = 0;

$(document).ready(function () {
  $("#methodSelect").change(function () {
    if ($(this).val() === "PUT") {
      $("#paramsTable").html($("<input>").attr({
        "type": "file",
        "name": "file",
        "id": "file",
        "placeholder": "选择文件"
      }));
    } else {
      $("#paramsTable").html(`<thead>
    <tr>
        <th>参数名</th>
        <th>值</th>
        <th><a href="javascript:addParamRow()">添加参数</a></th>
    </tr>
</thead>
<tbody>
</tbody>`);
    }
  });
});

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
  }).attr("icon", "delete").click(function () {
    newRow.remove();
  });
  actionCell.append(deleteButton);

  newRow.append(paramNameCell, paramValueCell, actionCell);
  $("#paramsTable").append(newRow);

  paramIndex++;
}

function sendRequest() {
  const methodSelect = $("#methodSelect");

  if (methodSelect.val() !== "PUT") {
    const params = {};
    $("#paramsTable tr").each(function (index, row) {
      const paramName = $(row).find("td:eq(0) input").val();
      const paramValue = $(row).find("td:eq(1) input").val();
      if (paramName && paramValue) {
        params[paramName] = paramValue;
      }
    });

    $.ajax({
      url: `${window.location.pathname.match(/\/docs(.*)\//)[1].startsWith("/api") ? "" : "/api"}${window.location.pathname.match(/\/docs(.*)\//)[1]}`,
      method: methodSelect.val(),
      data: params,
      success: function (data, status, jqxhr) {
        var contentType = jqxhr.getResponseHeader("content-type");
        if (contentType.startsWith("image/")) {
          var container = $("#responseTEXT");
          container.html("暂不支持查看图片");
        } else {
          renderResponseCard(data);
        }
      },
      error: function (xhr) {
        const response = xhr.responseText || "请求失败";
        try {
          const jsonResponse = JSON.parse(response);
          renderResponseCard(jsonResponse);
        } catch (e) {
          renderResponseCard(response);
        }
      }
    });
  } else {
    $.ajax({
      url: `${window.location.pathname.match(/\/docs(.*)\//)[1].startsWith("/api") ? "" : "/api"}${window.location.pathname.match(/\/docs(.*)\//)[1]}`,
      method: "PUT",
      data: $('#file')[0].files[0],
      processData: false,
      contentType: false,
      success: function (data, status, jqxhr) {
        var contentType = jqxhr.getResponseHeader("content-type");
        if (contentType.startsWith("image/")) {
          var container = $("#responseTEXT");
          container.html("暂不支持查看图片");
        } else {
          renderResponseCard(data);
        }
      },
      error: function (xhr) {
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
  $("#responseTEXT").html(syntaxHighlight(JSON.stringify(response, undefined, 4)));
}

function previewData() {
  var data = JSON.parse($('#responseTEXT').text() || '{"data":"这里空空如也，和你的头脑一样"}').data;
  var preElement = document.createElement('pre');
  preElement.textContent = data;
  document.body.appendChild(preElement);
  preElement.style.whiteSpace = 'pre-wrap';
  preElement.style.display = 'inline-block';
  html2canvas(preElement, {
    backgroundColor: '#f6f8fa'
  }).then(function (canvas) {
    var imgData = canvas.toDataURL('image/png');
    preElement.remove();
    message('<img src="' + imgData + '" style="clear:both;display:block;margin:auto;">');
  });
}
