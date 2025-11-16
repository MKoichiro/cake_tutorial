// ダイアログ
const dialogOpenBtns = document.querySelectorAll('.thread-delete-btn');
const dialog = document.querySelector('dialog');
const dialogCloseBtn = document.querySelector('#dialog-close-btn');
const dialogBackdrop = document.querySelector('#dialog-backdrop');
const dialogCancelBtn = document.querySelector('#dialog-cancel-btn');

const closeDialog = () => {
    dialog.classList.remove('open');
    dialogBackdrop.classList.remove('open');
};

dialogOpenBtns.forEach((btn) => {
    btn.addEventListener('click', (e) => {
        // 各ボタンの data-* 属性で動的に開く
        const dialogTitle = btn.dataset.dialogTitle;
        const dialogForm = btn.dataset.form;
        const content = btn.dataset.content;

        // dialog の各要素を取得
        const dialogFormElem = document.querySelector('#dialog-form');
        const deleteExecuteBtn = dialog.querySelector('#dialog-execute-btn');
        const dialogTitleElem = dialog.querySelector('#dialog-title');
        const dialogContentElem = dialog.querySelector('#dialog-content');

        // 属性・テキストを形成
        dialogFormElem.action = dialogForm;
        dialogContentElem.textContent = content;
        dialogTitleElem.textContent = dialogTitle;

        dialog.classList.add('open');
        dialogBackdrop.classList.add('open');
    });
});

dialogBackdrop.addEventListener('click', closeDialog);
dialogCloseBtn.addEventListener('click', closeDialog);
dialogCancelBtn.addEventListener('click', closeDialog);


// // edit user info: display_name
// const nameEnterEditBtn = document.querySelector('#name-enter-edit-btn');
// const nameSubmit = document.querySelector('#name-edit-submit');
// const nameDisplayModeContainer = document.querySelector('#name-display-mode');
// const nameEditModeContainer = document.querySelector('#name-edit-mode');
// const nameDisplay = document.querySelector('#name-display');
// const nameEditInput = document.querySelector('#name-edit-input');

// if (nameEnterEditBtn) {
//     nameEnterEditBtn.addEventListener('click', () => {
//         const currentValue = nameDisplay.textContent;
//         nameDisplayModeContainer.classList.add('inactive');
//         nameEditModeContainer.classList.add('active');
//         nameEditInput.value = currentValue;
//         nameEditInput.focus();
//     });
// }

// if (nameSubmit) {
//     nameSubmit.addEventListener('click', (e) => {
//         // ...
//         nameDisplayModeContainer.classList.remove('inactive');
//         nameEditModeContainer.classList.remove('active');
//     });
// }

// if (nameEditInput) {
//     nameEditInput.addEventListener('blur', (e) => {
//         // ...
//         nameDisplayModeContainer.classList.remove('inactive');
//         nameEditModeContainer.classList.remove('active');
//     });
// }


// // edit user info: email
// const emailEnterEditBtn = document.querySelector('#email-enter-edit-btn');
// const emailSubmit = document.querySelector('#email-edit-submit');
// const emailDisplayModeContainer = document.querySelector('#email-display-mode');
// const emailEditModeContainer = document.querySelector('#email-edit-mode');
// const emailDisplay = document.querySelector('#email-display');
// const emailEditInput = document.querySelector('#email-edit-input');

// if (emailEnterEditBtn) {
//     emailEnterEditBtn.addEventListener('click', () => {
//         const currentValue = emailDisplay.textContent;
//         emailDisplayModeContainer.classList.add('inactive');
//         emailEditModeContainer.classList.add('active');
//         emailEditInput.value = currentValue;
//         emailEditInput.focus();
//     });
// }

// if (emailSubmit) {
//     emailSubmit.addEventListener('click', (e) => {
//         // ...
//         emailDisplayModeContainer.classList.remove('inactive');
//         emailEditModeContainer.classList.remove('active');
//     });
// }

// if (emailEditInput) {
//     emailEditInput.addEventListener('blur', (e) => {
//         // ...
//         emailDisplayModeContainer.classList.remove('inactive');
//         emailEditModeContainer.classList.remove('active');
//     });
// }