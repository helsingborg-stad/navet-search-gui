import AutoFormatter from 'auto-formatter';

function handleStringFormatting(event) {
  const targetNode  = event.target;
  let   targetValue = targetNode.value;
  const firstTwo    = targetValue.substring(0, 2);

  if (targetValue.length === 10 || targetValue.length === 11) {
    const currentYear = String(new Date().getFullYear()).substring(2);

    targetValue = targetValue.replace(/[^0-9]/g, '');

    if (targetValue.length === 10 && !["19", "20"].includes(firstTwo)) {
      if (firstTwo > currentYear) {
        targetValue = `19${targetValue}`;
      } else {
        targetValue = `20${targetValue}`;
      }
    }
  }

  if (targetValue.length === 12) {
    const formattedValue = AutoFormatter.format({
      value: targetValue,
      format: 'XXXXXXXX-XXXX'
    });
    targetNode.value = formattedValue;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const targetNode = document.querySelector('#input_pnr-search-field');

  let isDeleting = false;
  targetNode.addEventListener('keydown', (event) => {
    if (event.key === 'Backspace' || event.key === 'Delete') {
      isDeleting = true;
    } else {
      isDeleting = false;
    }
  });
  
  targetNode.addEventListener('keyup', (event) => {
    if (isDeleting) {
      return;
    }
    handleStringFormatting(event);
  });

  targetNode.addEventListener('blur', (event) => {
    handleStringFormatting(event);
  });

  targetNode.addEventListener('paste', (event) => {
    handleStringFormatting(event);
  });
});
