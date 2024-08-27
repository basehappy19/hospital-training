document.addEventListener('DOMContentLoaded', () => {
    let canPostCourseInput = document.getElementById('canPostCourseInput');
    let canManageUserInput = document.getElementById('canManageUserInput');
    
    canPostCourseInput.addEventListener('click', () => {
        canPostCourseInput.removeAttribute("checked");
        if (canPostCourseInput.checked) {
            canPostCourseInput.value = 1;
        } else {
            canPostCourseInput.value = 0;
        }
    });

    canManageUserInput.addEventListener('click', () => {
        canManageUserInput.removeAttribute("checked");
        if (canManageUserInput.checked) {
            canManageUserInput.value = 1;
        } else {
            canManageUserInput.value = 0;
        }
    });
});
