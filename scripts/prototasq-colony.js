/* ---------------------------- */
/* ProtoTasq-Colony Integration */
/* ---------------------------- */

/* Rinkeby ColonyNetWork EtherRouter address: 0xD4C145EbdC7f072d10a07b8ea4515AF996EE437c

/* Given you have a loader to load the ABIs needed, a provider (ethers) it will look like this: */
/* Create an adapter (powered by ethers) */
//  const adapter = new EthersAdapter({ loader, provider, wallet: provider });

/* Then you can initialise the networkClient like this: */
// const networkClient = new ColonyNetworkClient({ adapter });
// await networkClient.init();

/* Use Mist/MetaMask/Cipher Browser provider (given web3 is injected in the web page) */
// const provider = new ethers.providers.Web3Provider(web3.currentProvider);
// const signer = provider.getSigner();
// const adapter = new EthersAdapter({ loader, provider, wallet: signer });


// ===============
// === DOMAINS ===
// ===============

// --- Create Domain ---
function pt_create_domain(domainId) {
	const domain = await colonyClient.getDomain.call({ domainId: domainId });
	colonyClient.addDomain.send({ parentSkillId: domain.localSkillId })
}


// ============= //
// === TASKS === //
// ============= //

// === Create Task === //
function pt_task_create(specificationHash, domainid) {
	const taskid = await colonyClient.createTask.send({
		specificationHash: specificationHash, domainId: domainid,
	});
	return taskid;
}

// === Get Task === //
function pt_task_get(taskid) {
	const task = await colonyClient.getTask.call({ taskId: taskid });
	return task;
}

// === Cancel Task === //
function pt_task_cancel(taskid) {
	result = await colonyClient.cancelTask.send({ taskId: taskid });
	return result;
}

// === Modify Task === //

// --- Set Task Manager ---
function pt_task_set_manager(taskId, managerAddress) {
	result = await colonyClient.setTaskRoleUser.send({
		taskId: taskId, role: 'MANAGER', user: managerAddress,
	});
	return result;
}

// --- Set Task Worker ---
function pt_task_set_worker(taskId, workerAddress) {
	result = await colonyClient.setTaskRoleUser.send({
	  taskId: taskId, role: 'WORKER', user: workerAddress,
	});
}

// --- Set Task Domain --- //
function pt_task_set_domain(taskId, domainId) {
	result = await colonyClient.setTaskDomain.send({ taskId: taskId, domainId: domainId, });
	return result;
}

// --- Set Task Skill ---
function pt_task_set_skill(taskId, skillId) {
	result = await colonyClient.setTaskSkill.send({ taskId: taskId, skillId: skillId });
	return result;
}

// =================== //
// === TASK RATING === //
// =================== //

// --- Get Salt ---
function pt_task_get_salt() {
	const salt = web3Utils.soliditySha3(getRandomString(10));
	// alternative: https://developer.mozilla.org/en-US/docs/Web/API/Crypto/getRandomValues
	return salt;
}

// --- Rate Task Abstract ---
function pt_task_rate(taskId, role, rating, salt) {
	const ratingSecret = await colonyClient.generateSecret.call({ salt, rating });
	result = await colonyClient.submitTaskWorkRating.send({
	  taskId: taskId, role: role, ratingSecret
	});
	console.log(result);
	return result;
}

// --- Worker Rate Task ---
function pt_task_worker_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'WORKER', rating, salt); return result;
}
// --- Manager Rate Task ---
function pt_task_manager_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'MANAGER', rating, salt); return result;
}
// --- Evaluator Rate Task ---
function pt_task_evaluator_rate(taskId, rating, salt) {
	result = cc_task_rate(taskId, 'EVALUATOR', rating, salt); return result;
}

// === Reveal Task Rating === //
function pt_task_reveal_rating(taskId, role, rating, salt) {
	result = await colonyClient.revealTaskWorkRating.send({ taskId: taskID, role: role, rating, salt });
	if (cc_debug) {console.log(result);}
	return result;
}

// === Get Task Rating === //
function pt_task_get_rating(taskId) {
	rating = const { count, timestamp } = await colonyClient.getTaskWorkRatings.call({ taskId: taskId });
	if (cc_debug) {console.log(rating);}
	return rating;
}

// --- Finalize Task --- //
function pt_task_finalize(taskId) {
	result = await colonyClient.finalizeTask.send({ taskId: taskId });
	if (cc_debug) {console.log(result);}
	return result;
}

// --- Claim Task Payout --- //
function pt_task_payout(taskId, role, tokenAddress)
	result = await colonyClient.claimPayout.send({ taskId: taskId, role: role, token: tokenAddress });
	if (cc_debug) {console.log(result);}
	return result;
}

// --- Claim Worker Payout --- //
function pt_task_payout_worker(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'WORKER', tokenAddress);
}
// --- Claim Manager Payout --- //
function pt_task_payout_manager(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'MANAGER', tokenAddress);
}
// --- Claim Evaluator Payout --- //
function pt_task_payout_evaluator(taskId, tokenAddress) {
	return cc_task_payout(taskId, 'EVALUATOR', tokenAddress);
}

// --- Claim All Payouts --- //
function pt_task_payout_all(taskId) {
	/* TODO: get token address for all roles */
	result[0] = cc_task_payout_worker(taskId, workerAddress);
	result[1] = cc_task_payout_manager(taskId, managerAddress);
	result[2] = cc_task_payout_evaluator(taskId, evaluatorAddress);
	return result;
}


// =================================== //
// === Multisignature Transactions === //
// =================================== //

// === Create Multisignature Operation === //
function pt_task_brief_multisig(taskId, specificationHash) {
	const op = await colonyClient.setTaskBrief.startOperation({
	  taskId: taskId, specificationHash: specificastionHash,
	});
	pt_task_multisig_sign(op);
	pt_task_multisig_export(op);
	console.log(op);
	return op;
}

// === Identify Required signees === //
function pt_task_brief_multisig(op) {
	op.requiredSignees; console.log(
	op.missingSignees;
}

// === Sign the Operation === //
function pt_task_multisig_sign(op) {
	result = await op.sign();
	console.log(result);
	return result;
}

// === Export Operation === //
function pt_task_multisig_export(op) {
	const json = op.toJSON();
	console.log(json); return json;
}

// === Restore Operation --- //
function pt_task_multisig_import(json)
	const op = await colonyClient.setTaskBrief.restoreOperation(json);
	return op;
}

// === Sign the Operation by the Other Party === //
function pt_task_multisig_brief_send(json) {
	op = pt_task_multisig_import(json);
	const { result } = await op.send();
	/* console.log(result); */
	if (result) {output = 'success';} else {output = 'failed';}
	console.log('MultiSignature Transaction Result: '+output);
	return result;
}
