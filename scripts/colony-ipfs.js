
// ============ //
// === IPFS === //
// ============ //

/* Using IPFS for a Task Specification */

/* The task specification is meant to be a description of work to be done that would be considered sufficient for a task payout. The exact format is left open; it could be a text file, .pdf, or other media. */

/* A natural way to use the specificationHash field is to point to a file hosted on IPFS. */

/* === Set up IPFS ===
For development with colonyJS, IPFS must be aded to your project

The easiest way to add ipfs is via npm:

yarn add ipfs

Then include it in your project's code: */

/* const IPFS = require('ipfs');
const ipfs = new IPFS(); */

/* This will run a full IPFS node in node.js or your browser. Heads up! Running your IPFS node in browser will require you to install the Buffer package seperately (while it's included in node.js by default). */

require('./scripts/buffer');

// ==== Alternative IPFS setup === //

/* You can also run a go node and access it via js-ipfs-api which should conform to the IPFS core interface specification.

If you decided to use the go node approach, this should get you a running installation of go-ipfs:

Download ipfs-update for your platform
Install IPFS | IPFS Docs
(for example curl -O https://dist.ipfs.io/ipfs-update/v1.5.2/ipfs-update_v1.5.2_darwin-amd64.tar.gz for MacOS)
Unpack and install: tar -xvzf ipfs-update_v1.5.2_darwin-amd64.tar.gz cd ipfs-update && ./install.sh
Get the latest version of IPFS: ipfs-update install latest
Check whether ipfs is installed properly
$ ipfs --version
ipfs version 0.4.15 */

// === Use External IPFS Node === //

// ref: https://itnext.io/build-a-simple-ethereum-interplanetary-file-system-ipfs-react-js-dapp-23ff4914ce4e
// using the infura.io node, otherwise ipfs requires you to run a //daemon on your own computer/server.

/* use IPFS or ipfsApi ? */
const IPFS = require('./scripts/ipfs-api.min');
const ipfs = new ipfsApi({ host: 'ipfs.infura.io', port: 5001, protocol: 'https' });

// ??? OR maybe ???
// var ipfs = window.IpfsApi({ host: 'ipfs.infura.io', port: 5001, protocol: 'https' });

// run with local daemon
// const ipfsApi = require('ipfs-api');
// const ipfs = new ipfsApi('localhost', '5001', { protocol:'http' });

export default ipfs;

// === Set the specificationHash ===

/* The IPFS hash is returned after adding the file to IPFS. Use that return to pass the specificationHash value on to the createTask method.

IPFS requires that files be uploaded as a Buffer, which is a binary representation of the data to host.

To create that buffer, the specification must first be 'converted' to a JSON string. */

/* // Prepare our data by passing our spec object as a JSON string to `Buffer`
const data = Buffer.from(JSON.stringify(spec));
// upload your file to IPFS
const files = await ipfs.files.add(data)
// set the hash when it's returned after upload
const { hash } = files[0]; */

// TODO: possible to create a directory for the project first ?
// Ref: https://discuss.ipfs.io/t/adding-a-file-while-preserving-filename-using-js-ipfs/2060/2

function cc_task_create_brief_hash(briefData) {
	// Prepare our data by passing our spec object as a JSON string to `Buffer`
	const data = Buffer.from(JSON.stringify(briefData));
	// upload your file to IPFS
	const files = await ipfs.files.add(data)
	// set the hash when it's returned after upload
	const { hash } = files[0];
	return hash;
}

// Create a new task with your IPFS hash set as the specificationHash
/* colonyClient.createTask.send({ specificationHash: hash });
You can also update the specificationHash by calling the setTaskBrief multisignature operation. See multisignature transactions for more details. */

// === Retrieve the task specification from IPFS ===

/* To retrieve the specification from IPFS for a task that's already been created, use the getTask method. */

/* const task = await colonyClient.getTask.call({ taskId })
// IPFS will provide a binary representation ('buffer') of our spec given the hash from our task
const buffer = await node.files.cat(`/ipfs/${task.specificationHash}`);
// You likely will want to parse the buffer back into a regular JS object:
const contents = JSON.parse(buffer.toString()); */

function cc_task_get_brief(taskId) {
	const task = await colonyClient.getTask.call({ taskId })
	const buffer = await ipfs.files.cat(`/ipfs/${task.specificationHash}`);
	const contents = JSON.parse(buffer.toString());
	return contents;
}
/* Aliases */
function cc_task_get_specification(hash) {return cc_task_get_brief(hash);}
function cc_task_get_spec(hash) {return cc_task_get_brief(hash);}


