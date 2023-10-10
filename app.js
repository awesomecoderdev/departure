const buffer = new ArrayBuffer(64);
const arr = new Uint8Array(buffer, 0, 16); // view first 16 bytes

console.log("arr", arr);
