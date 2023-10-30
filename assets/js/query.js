let paramIndex = 0;

function addParamRow() {
  const paramsTable = document.getElementById('paramsTable');
  const newRow = paramsTable.insertRow();

  const paramNameCell = newRow.insertCell();
  const paramNameInput = document.createElement('input');
  paramNameInput.type = 'text';
  paramNameInput.classList.add('mdui-textfield-input');
  paramNameInput.placeholder = '参数名';
  paramNameInput.name = `paramName-${paramIndex}`;
  paramNameCell.appendChild(paramNameInput);

  const paramValueCell = newRow.insertCell();
  const paramValueInput = document.createElement('input');
  paramValueInput.type = 'text';
  paramValueInput.classList.add('mdui-textfield-input');
  paramValueInput.placeholder = '值';
  paramValueInput.name = `paramValue-${paramIndex}`;
  paramValueCell.appendChild(paramValueInput);

  const actionCell = newRow.insertCell();
  const deleteButton = document.createElement('button');
  deleteButton.type = 'button';
  deleteButton.classList.add('mdui-btn', 'mdui-btn-icon', 'mdui-color-red');
  deleteButton.innerHTML = '<i class="mdui-icon material-icons">delete</i>';
  deleteButton.onclick = function() {
    paramsTable.deleteRow(newRow.rowIndex);
  };
  actionCell.appendChild(deleteButton);

  paramIndex++;
}

function sendRequest() {
    const methodSelect = document.getElementById('methodSelect');
    
    const paramsTable = document.getElementById('paramsTable');
    const params = {};
    for (let i = 1; i < paramsTable.rows.length; i++) {
      const paramName = paramsTable.rows[i].cells[0].querySelector('input').value;
      const paramValue = paramsTable.rows[i].cells[1].querySelector('input').value;
      if (paramName && paramValue) {
        params[paramName] = paramValue;
      }
    }
    
    const currentPath = window.location.pathname.match(/\/docs(.*)\//)[1];
    const url = `${currentPath.startsWith('/api') ? '' : '/api'}${currentPath}`; // 拼接 URL
  
    $.ajax({
      url: url,
      method: methodSelect.value,
      data: params,
      success: function(response) {
        renderResponseCard(response); // 将返回内容生成到前面的卡片内
      },
      error: function(xhr, status, error) {
        const response = xhr.responseText || '请求失败';
        try {
            const jsonResponse = JSON.parse(response);
            renderResponseCard(jsonResponse); // 将错误消息生成到前面的卡片内
          } catch (e) {
            // 如果无法解析为 JSON 对象，则直接显示原始内容
            renderResponseCard(response);
          }
      }
    });
}

function syntaxHighlight(json) {
  json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
  return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?)/g, function (match) {
      var cls = 'number';
      if (/^"/.test(match)) {
          if (/:$/.test(match)) {
              cls = 'key';
          } else {
              cls = 'string';
          }
      } else if (/true|false/.test(match)) {
          cls = 'boolean';
      } else if (/null/.test(match)) {
          cls = 'null';
      }
      return '<span class="' + cls + '">' + match + '</span>';
  });
}

function renderResponseCard(response) {
    const responseText = document.getElementById('responseTEXT');
    responseText.innerHTML = syntaxHighlight(JSON.stringify(response, undefined, 4));
}