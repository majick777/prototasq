
// ============= //
// === TASKS === //
// ============= //

/* The most useful abstraction within a colony is the task. Tasks are used to coordinate work, track reputation, and ultimately the only way to get paid through a colony. See tasks for a complete description of what tasks are within a colony. */

// === Create Task === //

/* A newly created task must be assigned to a domain and must reference a specificationHash for the task's completion. Also known as a "Task Brief", the task specification is a description of the work to be done and how that work will be evaluated. This can be any arbitrary hash string (32 bytes), but is especially suited be a unique IPFS content hash. See Using IPFS for details. */

/* The "root domain" of any colony is 1, and is the default value for domainId if unspecified. */

/* const { eventData: { taskId } } = await colonyClient.createTask.send({
  specificationHash: 'specification hash goes here', domainId: 1,
}); */

function cc_task_create(specificationHash, domainid) {
	const taskid = await colonyClient.createTask.send({
		specificationHash: specificationHash, domainId: domainid,
	});
	return taskid;
}

// === Get Task === //

/* Once a task has been created, it can be examined: */
/* const task = await colonyClient.getTask.call({ taskId: 1 }); */

function cc_task_get(taskid) {
	const task = await colonyClient.getTask.call({ taskId: taskid });
	return task;
}

// === Cancel Task === //

/* At any time before a task is finalized, the task can be canceled, which allows any funding to be returned to the colony and halts any further modification of the task. */
/* await colonyClient.cancelTask.send({ taskId: 1 }); */

function cc_task_cancel(taskid) {
	result = await colonyClient.cancelTask.send({ taskId: taskid });
	return result;
}

// === Modify Task === //

/* After the task has been created, the task may be modified to include additional data. This could be setting the task's worker or manager, or skill tag(s). */

// --- Set Task Manager ---
/* await colonyClient.setTaskRoleUser.send({
  taskId: 1, role: 'MANAGER', user: 'wallet address of manager',
}); */

function cc_task_set_manager(taskId, managerAddress) {
	result = await colonyClient.setTaskRoleUser.send({
		taskId: taskId, role: 'MANAGER', user: managerAddress,
	});
	return result;
}

// --- Set Task Worker ---
/* await colonyClient.setTaskRoleUser.send({
  taskId: 1, role: 'WORKER', user: 'wallet address of worker',
}); */

function cc_task_set_worker(taskId, workerAddress) {
	result = await colonyClient.setTaskRoleUser.send({
	  taskId: taskId, role: 'WORKER', user: workerAddress,
	});
}

// --- Set Task Domain --- //
/* await colonyClient.setTaskDomain.send({ taskId: 1, domainId: 2, }); */

function cc_task_set_domain(taskId, domainId) {
	result = await colonyClient.setTaskDomain.send({ taskId: taskId, domainId: domainId, });
	return result;
}

// --- Set Task Skill ---
/* await colonyClient.setTaskSkill.send({ taskId: 1, skillId: 5 }); */
function cc_task_set_skill(taskId, skillId) {
	result = await colonyClient.setTaskSkill.send({ taskId: taskId, skillId: skillId });
	return result;
}

/* Modification with Multi-sig Operations
Important changes to a task must be approved by multiple people. Task changes requiring two signatures are: */

// Changing the task Brief (Manager and Worker)
// Changing or setting the task Due Date (Manager and Worker)
// Changing or setting the Worker's payout (Manager and Worker)
// Changing or setting the Evaluator's payout (Manager and Evaluator)

/* Attempting to use these methods without a MultisigOperation will throw an error. You can learn more about Multisignature transactions in colonyJS.
https://github.com/JoinColony/colonyJS/blob/master/colonyjs/docs-multisignature-transactions
*/


// =================== //
// === TASK RATING === //
// =================== //

/* After the work has been submitted (or the due date has passed), the work rating period begins. */

/* Task payouts are determined by work rating, which is currently implemented as "5-star" system, but which will change to a "3-star" system in the future. */

/* The Evaluator reviews the work done and submits a rating for the Worker.
The Worker considers the task assignment and submits a rating for the Manager.
Because work ratings are on-chain, they follow a Commit and Reveal pattern in which ratings are obscured to prevent them from influencing each other. */

// === Commit Task Rating === //

/* During the Commit period, hidden ratings are submitted to the blockchain. The commit period lasts 5 days. */
/* const ratingSecret = await colonyClient.generateSecret.call({ salt, rating });
await colonyClient.submitTaskWorkRating.send({  taskId: 1, role: 'WORKER', ratingSecret }); */

// TODO: a "get salt" function
function cc_task_get_salt() {
	// const salt = web3Utils.soliditySha3(getRandomString(10));
	// alternative: https://developer.mozilla.org/en-US/docs/Web/API/Crypto/getRandomValues
	return salt;
}

function cc_task_rate(taskId, role, rating, salt) {
	const ratingSecret = await colonyClient.generateSecret.call({ salt, rating });
	result = await colonyClient.submitTaskWorkRating.send({
	  taskId: taskId, role: role, ratingSecret
	});
	if (cc_debug) {console.log(result);}
	return result;
}

function cc_task_worker_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'WORKER', rating, salt); return result;
}
function cc_task_manager_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'MANAGER', rating, salt); return result;
}
function cc_task_evaluator_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'EVALUATOR', rating, salt); return result;
}



// === Reveal Task Rating === //

/* During the Reveal period, users submit a transaction to reveal their rating. */
/* await colonyClient.revealTaskWorkRating.send({
  taskId: 1, role: 'WORKER', rating, salt,
}); */

function cc_task_reveal_rating(taskId, role, rating, salt) {
	result = await colonyClient.revealTaskWorkRating.send({ taskId: taskID, role: role, rating, salt });
	if (cc_debug) {console.log(result);}
	return result;
}


/* During the rating period, if either party fails to commit or reveal their rating, their counterpart is given the highest possible rating, and their own rating is penalized. */

// === Get Task Rating === //

/* It's easy to check the status of a task during the rating period: */
/* const { count, timestamp } = await colonyClient.getTaskWorkRatings.call({ taskId: 1 }); */

// ??? CHECK RETURN VALUE FORMAT ???
function cc_task_get_rating(taskId) {
	rating = const { count, timestamp } = await colonyClient.getTaskWorkRatings.call({ taskId: taskId });
	if (cc_debug) {console.log(rating);}
	return rating;
}

// --- Finalize Task --- //

/* After the rating period has finished, the task may be finalized, which prevents any further task modifications and allows each role to claim their payout. */

/* await colonyClient.finalizeTask.send({ taskId: 1 }); */

function cc_task_finalize(taskId) {
	result = await colonyClient.finalizeTask.send({ taskId: taskId });
	if (cc_debug) {console.log(result);}
	return result;
}

// --- Claim Task Payout --- //

/* await colonyClient.claimPayout.send({
  taskId: 1, role: 'WORKER', token: 'token contract address',
}); */

function cc_task_payout(taskId, role, tokenAddress)
	result = await colonyClient.claimPayout.send({ taskId: taskId, role: role, token: tokenAddress });
	if (cc_debug) {console.log(result);}
	return result;
}

function cc_task_payout_worker(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'WORKER', tokenAddress);
}

function cc_task_payout_manager(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'MANAGER', tokenAddress);
}

function cc_task_payout_evaluator(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'EVALUATOR', tokenAddress);
}

function cc_task_payout_all(taskId) {
	/* TODO: get token address for all roles */
	result[0] = cc_task_payout_worker(taskId, workerAddress);
	result[1] = cc_task_payout_manager(taskId, managerAddress);
	result[2] = cc_task_payout_evaluator(taskId, evaluatorAddress);
	return result;
}