// notes.js
// ���� �������� ��� ������ � ����� 14
// ���� �������� ������� � ������� ����� page.php
$(function() {

  var notes_form = $('#notes_form');

  notes_form.submit(function(){
    $.ajax({
      url: 'ajax/notes.php',
      type: 'POST',
      dataType: 'text',
      data: {
        page_id: page_id,
        notes: $('#notes').val()
      },
      success: function(response) {
        if (response === 'true') {
          var notes_message = $('#notes_message');
          if (notes_message.length !== 0) {
              notes_message.html('���� ������� ����� ���������.');
          } else {
            notes_form.prepend('<p id="notes_message" class="alert alert-success">���� ������� ���������.</p>');
          }
        } else {
          // �����-���� ��������
        }
      } // �������, ����������� � ������ ������
    }); // Ajax
    return false;
  }); // ���������� ������� submit()

}); // �������� ��������� �������  