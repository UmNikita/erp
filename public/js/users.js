const searchField = document.querySelector('.js-user-search');
let timeout;
const usersTbodyHTML = document.querySelector('.js-users-table tbody');

class UserTableElement {
  constructor(user) {
    this.id = user.userId;
    this.firstLetter = user.userFirstLetter;
    this.name = user.userName;
    this.email = user.userEmail;
    this.department = user.userDepartment;
    this.status = user.userStatus;
    this.datetime = user.userDatetime;
    this.change = user.userChange;
    this.password = user.userPassword;
    this.isCurrent = user.userIsCurrent;
    this.isBtnActivate = user.userIsBtnActivate;
    this.btnIsActivate = user.userBtnIsActivate;
  }
  toRowHTML() {
    return `
        <tr data-user="${this.id}">
            <td>
                <div class="user-cell">
                <div class="avatar-small">${this.firstLetter ?? ''}</div>
                <div>
                    <b class="user-t-name">${this.name ?? ''}</b>
                    <div class="muted user-t-email">${this.email ?? ''}</div>
                </div>
                </div>
            </td>

            <td class="user-t-department">${this.department ?? ''}</td>

            <td>
                ${
                this.status
                    ? `<span class="status off">Деактивирован</span>`
                    : `<span class="status">Активен</span>`
                }
            </td>

            <td class="muted user-t-datetime">${this.datetime ?? ''}</td>

            <td>
                <div class="row-actions">
                <a class="btn secondary btn-sm user-t-btn-e" href="/edit/${this.id}">Изменить</a>
                <a class="btn secondary btn-sm user-t-btn-p" href="/password/${this.id}">Пароль</a>

                ${
                    this.isCurrent
                    ? `<span class="muted">Текущий аккаунт</span>`
                    : `<button type="button" class="btn secondary btn-sm js-toggle-user">
                        ${this.btnIsActivate ? 'Активировать' : 'Деактивировать'}
                        </button>`
                }
                </div>
            </td>
        </tr>
    `;
  }
}

const setUserTableData = () => {
    const usersTableHTML = usersTbodyHTML.querySelectorAll('tr');
    let userTableElements = [];
    usersTableHTML.forEach(element => {
        let user = {};
        user.userId = element.getAttribute('data-user');
        user.userFirstLetter = element.querySelector('.avatar-small').innerHTML;
        user.userName = element.querySelector('.user-t-name').innerHTML;
        user.userEmail = element.querySelector('.user-t-email').innerHTML;
        user.userDepartment = element.querySelector('.user-t-department').innerHTML;
        user.userStatus = element.querySelector('.status').classList.contains('off');
        user.userDatetime = element.querySelector('.user-t-datetime').innerHTML;
        user.userChange = element.querySelector('.user-t-btn-e').getAttribute('href');
        user.userPassword = element.querySelector('.user-t-btn-p').getAttribute('href');
        user.userIsCurrent = element.querySelector('.user-t-btn-current') ? true : false;
        user.userIsBtnActivate = element.querySelector('.js-toggle-user') ? true : false;
        user.userBtnIsActivate = false;
        if(user.userIsBtnActivate) {
            const htmlText = element.querySelector('.js-toggle-user').innerHTML;
            if(htmlText == "Активировать") {
                user.userBtnIsActivate = true;
            }
        }

        userTableElements.push(new UserTableElement(user));
    });
    return userTableElements;
}

userTableElements = setUserTableData();

const searchUsers = (query) => {
    const searchText = query.toLowerCase();

    return userTableElements.filter(user =>
        user.name?.toLowerCase().includes(searchText) ||
        user.email?.toLowerCase().includes(searchText) ||
        user.description?.toLowerCase().includes(searchText) ||
        user.department?.toLowerCase().includes(searchText)
    );
}

const renderTable = (userTableElements) => {
    usersTbodyHTML.replaceChildren();
    userTableElements.forEach(element => {
        usersTbodyHTML.insertAdjacentHTML('beforeend', element.toRowHTML());
    });
}

searchField.addEventListener('input', (e) => {
    clearTimeout(timeout);

    timeout = setTimeout(() => {
        const users = searchUsers(e.target.value)
        renderTable(users);
    }, 500);
});



const setBtnTableData = () => {
    const usersTableHTML = usersTbodyHTML.querySelectorAll('tr');
    usersTableHTML.forEach(element => {
        let id = element.getAttribute('data-user');
        let btn = element.querySelector('button');
        if(btn != null) {
            btn.addEventListener('click', () => deactivate(id, btn))
        }
    });
}

const deactivate = (id, button) => {
    if(confirm("Вы уверены что хотите деактивировать аккаунт?")) {
        fetch(`/home/users/deactivate`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                id: id
            })
        })
        .then(res => res.json())
        .then(data => {
            button.remove()
        });
    }
}

const setBtnMatrix = () => {
    const matrixHTML = document.querySelectorAll('.access-matrix tbody tr');
    matrixHTML.forEach(element => {
        let id = element.getAttribute('data-id');
        let tdHTML = element.querySelectorAll('td');
        tdHTML.forEach(el => {
            if(el.className != '') {
                const btn = el.querySelector('button');
                btn.addEventListener('click', () => buttonMatrixHolder(id, el.className, btn));
            }
        })
        
    });
}

const buttonMatrixHolder = (id, name, btn) => {
    const isActive = btn.getAttribute('data-value');
    fetch(`/home/users/matrix/holder`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            id: id,
            name: name,
            isActive: isActive
        })
    })
    .then(res => res.json())
    .then(data => {
        console.log(data)
        if(isActive == "true") {
            btn.classList.remove('on');
            btn.setAttribute('data-value', 'false')
        }
        else {
            btn.classList.add('on');
            btn.setAttribute('data-value', 'true')
        }
    });
}

setBtnTableData();
setBtnMatrix();