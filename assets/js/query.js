let paramIndex = 0;

function addParamRow() {
  const paramsTable = document.getElementById('paramsTable');
  const newRow = paramsTable.insertRow();

  const paramNameCell = newRow.insertCell();
  const paramNameInput = document.createElement('mdui-text-field');
  paramNameInput.label = '参数名';
  paramNameInput.name = `paramName-${paramIndex}`;
  paramNameCell.appendChild(paramNameInput);

  const paramValueCell = newRow.insertCell();
  const paramValueInput = document.createElement('mdui-text-field');
  paramValueInput.label = '值';
  paramValueInput.name = `paramValue-${paramIndex}`;
  paramValueCell.appendChild(paramValueInput);

  const actionCell = newRow.insertCell();
  const deleteButton = document.createElement('mdui-button-icon');
  deleteButton.setAttribute('icon', 'delete');
  deleteButton.setAttribute('style', 'color: red');
  deleteButton.onclick = function () {
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
    const paramName = paramsTable.rows[i].cells[0].querySelector('input') ? paramsTable.rows[i].cells[1].querySelector('input').value : '';
    const paramValue = paramsTable.rows[i].cells[1].querySelector('input') ? paramsTable.rows[i].cells[1].querySelector('input').value : '';
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
    success: function (response) {
      renderResponseCard(response); // 将返回内容生成到前面的卡片内
    },
    error: function (xhr, status, error) {
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

function renderResponseCard(response) {
  $('#responseTEXT').val(JSON.stringify(response, undefined, 4));
}