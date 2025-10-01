const dbName = 'voucher_db';
const dbVersion = 1;
let db;

const openRequest = indexedDB.open(dbName, dbVersion);

export function openDatabase() {
    return new Promise((resolve, reject) => {
        openRequest.onsuccess = (e) => {
            db = e.target.result;
            resolve(db);
        };

        openRequest.onerror = (e) => {
            console.error('Error opening database:', e);
            reject('Error opening database');
        };

        openRequest.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('myStore')) {
                db.createObjectStore('myStore', { keyPath: 'id' });
            }
        };
    });
}

export function clearData() {
    return new Promise((resolve, reject) => {
        openRequest.onsuccess = function(event) {
            let db = event.target.result;
            let objectStoreNames = db.objectStoreNames;

            for (let storeName of objectStoreNames) {
                let transaction = db.transaction(storeName, "readwrite");
                let objectStore = transaction.objectStore(storeName);
                objectStore.clear();
            }
        };

        openRequest.onerror = function(event) {
            console.error("Ошибка при открытии IndexedDB:", event.target.error);
        };
    });
}

export function saveData(key, data) {
    return new Promise((resolve, reject) => {
        if (!db) {
            reject('Database is not initialized');
            return;
        }

        const transaction = db.transaction(['myStore'], 'readwrite');
        const store = transaction.objectStore('myStore');
        const request = store.put({ id: key, data: data });

        request.onsuccess = () => {
            resolve();
        };

        request.onerror = (e) => {
            console.error('Error saving data:', e.target.error);
            reject(e.target.error);
        };
    });
}

export function getData(key) {
    return new Promise((resolve, reject) => {
        if (!db) {
            reject('Database is not initialized');
            return;
        }

        const transaction = db.transaction(['myStore'], 'readonly');
        const store = transaction.objectStore('myStore');
        const request = store.get(key);

        request.onsuccess = (e) => {
            resolve(e.target.result);
        };

        request.onerror = (e) => {
            console.error('Error fetching data:', e.target.error);
            reject(e.target.error);
        };
    });
}
