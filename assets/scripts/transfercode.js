const showError = (message) => {
  const errorContainer = document.getElementById('tc-error-container');
  const errorMessage = document.getElementById('tc-error-message');
  const successContainer = document.getElementById('tc-success-container');
  
  // Hide success, show error
  successContainer.classList.add('hidden');
  errorMessage.textContent = message;
  errorContainer.classList.remove('hidden');
};

const showSuccess = (code) => {
  const errorContainer = document.getElementById('tc-error-container');
  const successContainer = document.getElementById('tc-success-container');
  const codeDisplay = document.getElementById('tc-code-display');
  
  // Hide error, show success
  errorContainer.classList.add('hidden');
  codeDisplay.textContent = code;
  successContainer.classList.remove('hidden');
};

const applyTransferCode = (code) => {
  const transferCodeInput = document.getElementById('transferCode');
  if (transferCodeInput) {
    transferCodeInput.value = code;
    
    // Add a subtle highlight effect to show it was filled
    transferCodeInput.classList.add('field-updated');
    setTimeout(() => {
      transferCodeInput.classList.remove('field-updated');
    }, 2000);
    
    // Close the offcanvas
    const offcanvasElement = document.getElementById('transferCodeService');
    const offcanvas = bootstrap.Offcanvas.getInstance(offcanvasElement);
    if (offcanvas) {
      offcanvas.hide();
    }
    
    // Scroll to the transfer code field so user sees it was filled
    transferCodeInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
  }
};

const setLoadingState = (isLoading) => {
  const submitBtn = document.getElementById('tc-submit-btn');
  const btnText = document.getElementById('tc-btn-text');
  const btnSpinner = document.getElementById('tc-btn-spinner');
  
  submitBtn.disabled = isLoading;
  btnText.textContent = isLoading ? 'Processing...' : 'Get transferCode';
  
  if (isLoading) {
    btnSpinner.classList.remove('hidden');
  } else {
    btnSpinner.classList.add('hidden');
  }
};

const transferCodeForm = document.getElementById('tc-offcanvas');
transferCodeForm.addEventListener('submit', (event) => {
  event.preventDefault();

  // Hide any previous messages
  document.getElementById('tc-error-container').classList.add('hidden');
  document.getElementById('tc-success-container').classList.add('hidden');

  // Show loading state
  setLoadingState(true);

  const formData = new FormData(transferCodeForm);
  const data = Object.fromEntries(formData.entries());

  const user = data.user;
  const api_key = data.api_key;
  const amount = data.amount;

  // Validate amount
  if (!amount || amount <= 0) {
    showError('Please enter a valid amount greater than 0');
    setLoadingState(false);
    return;
  }

  fetch('https://www.yrgopelag.se/centralbank/withdraw', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ user, api_key, amount })
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then((data) => {
      setLoadingState(false);
      
      if (data.error) {
        // Show specific error from API
        showError(data.error);
      } else if (data.transferCode) {
        // Show success with transferCode
        showSuccess(data.transferCode);
        
        // Set up the "Use This Code" button
        const useCodeBtn = document.getElementById('use-code-btn');
        if (useCodeBtn) {
          useCodeBtn.onclick = () => applyTransferCode(data.transferCode);
        }
        
        transferCodeForm.reset();
      } else {
        // Unexpected response format
        showError('Unexpected response from bank. Please try again.');
      }
    })
    .catch((error) => {
      setLoadingState(false);
      
      // Handle network errors
      if (error.message.includes('Failed to fetch')) {
        showError('Network error. Please check your internet connection and try again.');
      } else if (error.message.includes('HTTP error')) {
        showError('Server error. The bank service may be temporarily unavailable.');
      } else {
        showError('An unexpected error occurred. Please try again.');
      }
      
      console.error('Transfer code error:', error);
    });
});