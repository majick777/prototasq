
// =================================== //
// === Multisignature Transactions === //
// =================================== //

/* Some contract functions rely on what's known as a Multisignature in order to process a transaction. The implementation for some of the task-related functions in colonyNetwork is based on simple-multisig.

In order to use these functions, we need to create contract data to call our target contract function, gather parameters needed for executing the change on the contract, find which addresses we need signatures from, get each party to sign a transaction in a specific format, and finally, collate these signatures and send them off in one single transaction.

Wow, that's convoluted!

Sad Vitalik

Multisignature support in colonyJS
Thankfully, the ContractClient makes this much simpler for us by providing the MultisigSender and MultisigOperation.

A MultisigSender is an extension of the Sender; it provides methods with which it's possible to start and restore a MultisigOperation.

The basic idea is that we'll start an operation based on a particular function, with certain parameters (e.g. setting the brief of task 1 to 'the new specification hash'), then get that operation signed and sent off.

That's more like it!

Cool Vitalik Parrot

Example: setting the task brief
An example of a MultisigSender on the Colony Client is setTaskBrief. We'll need signatures from the Manager and Worker in order to change the task brief. */

// === Create Multisignature Operation === //

/* const op = await colonyClient.setTaskBrief.startOperation({
  taskId: 1,
  specificationHash: 'the new specification hash',
}); */

function cc_task_brief_multisig(taskId, specificationHash) {
	const op = await colonyClient.setTaskBrief.startOperation({
	  taskId: taskId, specificationHash: specificastionHash,
	});
	return op;
}

/* -> MultisigOperation
Let's break that down:

// const op = await colonyClient
//
// .setTaskBrief
// ^ The MultisigSender
//
// .startOperation(
// ^ Creates an MultisigOperation
//
// { taskId: 1, specificationHash: 'the new specification hash' }
// ^ The parameters we're calling the Sender with
//
// );


// === Identify required signees === //

/* We can determine which wallets can to sign the operation by checking the requiredSignees and missingSignees properties. */

// console.log(op.requiredSignees);

// -> ['0x123...', '0x987...'];
//    ^ Both of these addresses need to sign it...

// console.log(op.missingSignees);
// -> ['0x987...'];
//    ^ This address hasn't signed it yet!

function cc_task_brief_multisig(taskId, specificationHash) {
	op = cc_task_multisig_start(taskId, specificationHash);
	op.requiredSignees;
	op.missingSignees;
}

// === Sign the Operation ===

/* It's very simple to sign it:

// This will sign the operation with the current wallet.
await op.sign();
Now the other party needs to sign it; we'll probably need to recreate the operation on another instance of your app.

You can skip the next step if you can simply change the current wallet on the same app instance. */

function ccc_task_multisig_sign() {
	op = cc_task_brief_multisig(
	result = await op.sign();
}

// === Export/restore the Operation for the Other Party === //

/* Firstly, we'll need to export some JSON from the MultisigOperation we want to restore: */

/* const json = op.toJSON();
// -> "{ "nonce": 0, "payload": {...}, "signers": {...} }"
We can restore this elsewhere with the appropriate MultisigSender:

const op = await colonyClient.setTaskBrief.restoreOperation(json);
// -> MultisigOperation (with the same parameters and the first signature already in place) */


// === Sign the Operation by the Other Party) ===

/* Now the other signature can be added, and we can probably send it! */

/* await op.sign(); */

/* console.log(op.missingSignees);
// -> []
//    ^ We have all the signatures we need */


// === Send the Transaction === //

/*
// This works just like a regular Sender:
const { successful } = await op.send();
// -> true */

function cc_task_multisig_brief_send(taskId) {
	const { result } = await op.send();
	if (cc_debug) {
		if (result) {output = 'success';} else {output = 'failed';}
		console.log('MultiSignature Transaction Result: '+output);
	}
	return result;
}

/*
// We can also add transaction options as a parameter, e.g.:
// await op.send({ gasLimit: 2500000 });

// We can also see that our change took effect:
const task = await colonyClient.getTask.call({ taskId: 1 });
// -> { id: 1, specificationHash: 'the new specification hash', ... } */


// === Contract State Changes === //

/* It's important to understand that the data that is used to create signed messages in these operations related to the contract state at a particular point in time.

While signatures are being collected, at least two things can happen that might cause the operation to fail:

Another MultisigOperation is successfully sent on the contract, increasing the nonce value
The users assigned to the manager/worker/evaluator for the task change
If the nonce value changes, the operation will need to be signed again by both parties.

If one of the assigned users changes for a task, the new user will need to sign it (we won't need the signature from the user no longer assigned).

The MultisigOperation can refresh these values in order to help prevent sending a transaction that will fail.

// Example: two operations with the same nonce:
console.log(firstOp._nonce); // 1
console.log(secondOp._nonce); // 1

// And no missing signees:
console.log(firstOp.missingSignees); // []
console.log(secondOp.missingSignees); // []

// We can send the first operation successfully:
await firstOp.send();
// -> { successful: true }

// The second operation can be refreshed:
await secondOp.refresh();

// The nonce should have been incremented:
console.log(secondOp._nonce); // 2

// And the signers should have been reset:
console.log(firstOp.missingSignees); // ['0x...', '0x...']
It's worth noting that starting a new operation or sending an existing operation will always trigger a refresh first, so this can reset the (now invalid) signers.

If desired, we can make the resetting of signers more explicit by attaching a callback:

const op = await colonyClient.setTaskBrief.startOperation(
  {
    taskId: 1,
    specificationHash: 'the new specification hash',
  },
  {}, // The signers, empty in this case
  () => {
    console.log('The signers were reset!');
  },
);